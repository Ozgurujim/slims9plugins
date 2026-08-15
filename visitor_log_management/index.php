<?php
/**
 * 
 * Visitor Log Management - Main Interface
 * Deletion of redundant visitor log records
 * Created by gurujim 27/06/2026
 */

// No additional privilege check - SLiMS authentication already ensures user is logged in
$can_read = true;
$can_write = true;
$page_title = __('Visitor Log Management');

// Get current page URL for form actions
$current_url = $_SERVER['REQUEST_URI'];

// Handle deletion
$message = '';
$message_type = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    switch ($_POST['action']) {
        case 'delete_by_date':
            $days = (int)$_POST['days_old'];
            if ($days > 0) {
                $sql = "DELETE FROM visitor_count WHERE DATEDIFF(NOW(), checkin_date) > $days";
                $dbs->query($sql);
                $deleted = $dbs->affected_rows;
                $message = sprintf(__('Deleted %d record(s) older than %d days'), $deleted, $days);
                $message_type = 'success';
            }
            break;
            
        case 'delete_by_criteria':
            $criteria = array();
            
            if (!empty($_POST['member_name'])) {
                $member_name = $dbs->real_escape_string($_POST['member_name']);
                $criteria[] = "member_name LIKE '%$member_name%'";
            }
            if (!empty($_POST['member_id'])) {
                $member_id = $dbs->real_escape_string($_POST['member_id']);
                $criteria[] = "member_id = '$member_id'";
            }
            if (!empty($_POST['institution'])) {
                $institution = $dbs->real_escape_string($_POST['institution']);
                $criteria[] = "institution LIKE '%$institution%'";
            }
            if (!empty($_POST['room_code'])) {
                $room_code = $dbs->real_escape_string($_POST['room_code']);
                $criteria[] = "room_code = '$room_code'";
            }
            if (!empty($_POST['date_from']) && !empty($_POST['date_until'])) {
                $date_from = $dbs->real_escape_string($_POST['date_from']);
                $date_until = $dbs->real_escape_string($_POST['date_until']);
                $criteria[] = "DATE(checkin_date) BETWEEN '$date_from' AND '$date_until'";
            }
            
            if (count($criteria) > 0) {
                $sql = "DELETE FROM visitor_count WHERE " . implode(' AND ', $criteria);
                $dbs->query($sql);
                $deleted = $dbs->affected_rows;
                $message = sprintf(__('Deleted %d record(s) matching criteria'), $deleted);
                $message_type = 'success';
            } else {
                $message = __('Please specify at least one criteria for deletion');
                $message_type = 'warning';
            }
            break;
            
        case 'delete_all':
            if (isset($_POST['confirm_all']) && $_POST['confirm_all'] == 'DELETE ALL') {
                $sql = "TRUNCATE TABLE visitor_count";
                $dbs->query($sql);
                $deleted = $dbs->affected_rows;
                $message = sprintf(__('Deleted ALL visitor records. Total affected: %d'), $deleted);
                $message_type = 'warning';
            } else {
                $message = __('Confirmation required. Please type "DELETE ALL" to confirm.');
                $message_type = 'error';
            }
            break;
    }
}

// Get statistics
$stats = array();
$total_result = $dbs->query("SELECT COUNT(*) FROM visitor_count");
$stats['total'] = $total_result ? $total_result->fetch_row()[0] : 0;
$today_result = $dbs->query("SELECT COUNT(*) FROM visitor_count WHERE DATE(checkin_date) = CURDATE()");
$stats['today'] = $today_result ? $today_result->fetch_row()[0] : 0;
$week_result = $dbs->query("SELECT COUNT(*) FROM visitor_count WHERE YEARWEEK(checkin_date) = YEARWEEK(CURDATE())");
$stats['this_week'] = $week_result ? $week_result->fetch_row()[0] : 0;
$month_result = $dbs->query("SELECT COUNT(*) FROM visitor_count WHERE MONTH(checkin_date) = MONTH(CURDATE()) AND YEAR(checkin_date) = YEAR(CURDATE())");
$stats['this_month'] = $month_result ? $month_result->fetch_row()[0] : 0;
$oldest_result = $dbs->query("SELECT MIN(checkin_date) FROM visitor_count");
$stats['oldest'] = $oldest_result ? $oldest_result->fetch_row()[0] : null;
$newest_result = $dbs->query("SELECT MAX(checkin_date) FROM visitor_count");
$stats['newest'] = $newest_result ? $newest_result->fetch_row()[0] : null;

// Get rooms for dropdown
$rooms = array();
$room_q = $dbs->query("SELECT unique_code, name FROM mst_visitor_room ORDER BY name");
if ($room_q) {
    while ($room = $room_q->fetch_assoc()) {
        $rooms[] = $room;
    }
}
?>

<div class="menuBox">
    <div class="menuBoxInner">
        <div class="per_title">
            <h2><?php echo $page_title; ?></h2>
        </div>
        
        <?php if ($message): ?>
        <div class="infoBox <?php echo $message_type; ?>" style="margin: 10px 0;">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="infoBox" style="background: #f2ffe3;">
            <strong><?php echo __('Visitor Statistics'); ?></strong>
            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 10px;">
                <div><strong><?php echo __('Total records:'); ?></strong> <?php echo number_format($stats['total']); ?></div>
                <div><strong><?php echo __('Today:'); ?></strong> <?php echo number_format($stats['today']); ?></div>
                <div><strong><?php echo __('This week:'); ?></strong> <?php echo number_format($stats['this_week']); ?></div>
                <div><strong><?php echo __('This month:'); ?></strong> <?php echo number_format($stats['this_month']); ?></div>
                <?php if ($stats['oldest']): ?>
                <div><strong><?php echo __('Oldest record:'); ?></strong> <?php echo $stats['oldest']; ?></div>
                <div><strong><?php echo __('Newest record:'); ?></strong> <?php echo $stats['newest']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="infoBox" style="margin-top: 20px;">
            <strong><?php echo __('Note:'); ?></strong>
            <ul style="margin: 5px 0 0 20px;">
                <li><?php echo __('Deletions are permanent and cannot be undone. You should save all Visitor Reports and backup your database before any deletion.'); ?></li>
                <li><?php echo __('Before deletion Visitor Reports are available under the Reporting menu.'); ?></li>
                <li><?php echo __('This tool is designed for periodic cleanup of old visitor records.'); ?></li>
            </ul>
        </div>
		
        <div class="sub_section">
            <!-- Delete by Date Range -->
            <div class="card" style="margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;">
                <div class="card-header" style="background: #f5f5f5; padding: 10px; border-bottom: 1px solid #ddd;">
                    <h3 style="color: #037fbc;"><?php echo __('Delete records older than ?? days'); ?></h3>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <form method="POST" action="<?php echo $current_url; ?>" onsubmit="return confirm('Are you sure you want to delete records older than the specified days? This cannot be undone.');">
                        <input type="hidden" name="action" value="delete_by_date">
                        <div class="form-group">
                            <label><?php echo __('Delete records older than (days):'); ?></label>
                            <input type="number" name="days_old" min="1" max="3650" value="90" style="width: 100px;" required>
                            <button type="submit" class="btn btn-warning"><?php echo __('Delete Old Records'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Delete by Custom Criteria -->
            <!-- <div class="card" style="margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;">
                <div class="card-header" style="background: #f5f5f5; padding: 10px; border-bottom: 1px solid #ddd;">
                    <h3><?php echo __('Delete Records by Criteria'); ?></h3>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <form method="POST" action="<?php echo $current_url; ?>" onsubmit="return confirm('Are you sure you want to delete records matching these criteria? This cannot be undone.');">
                        <input type="hidden" name="action" value="delete_by_criteria">
                        <table class="table table-bordered" style="width: 100%;">
                            <tr>
                                <td width="150"><label><?php echo __('Member Name contains:'); ?></label></td>
                                <td><input type="text" name="member_name" class="form-control" style="width: 100%;"></td>
                            </tr>
                            <tr>
                                <td><label><?php echo __('Member ID:'); ?></label></td>
                                <td><input type="text" name="member_id" class="form-control" style="width: 100%;"></td>
                            </tr>
                            <tr>
                                <td><label><?php echo __('Institution contains:'); ?></label></td>
                                <td><input type="text" name="institution" class="form-control" style="width: 100%;"></td>
                            </tr>
                            <tr>
                                <td><label><?php echo __('Room:'); ?></label></td>
                                <td>
                                    <select name="room_code" class="form-control">
                                        <option value=""><?php echo __('-- All Rooms --'); ?></option>
                                        <?php foreach ($rooms as $room): ?>
                                        <option value="<?php echo $room['unique_code']; ?>"><?php echo $room['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                
    
                            
    
                            </td>
                            </tr>
                            <tr>
                                <td><label><?php echo __('Date Range:'); ?></label></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <input type="date" name="date_from" class="form-control">
                                        <span><?php echo __('to'); ?></span>
                                        <input type="date" name="date_until" class="form-control">
                                    </div>
                                
    
                            
    
                            </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="submit" class="btn btn-danger"><?php echo __('Delete Matching Records'); ?></button>
                                
    
                            
    
                            </tr>
                     
                    </form>
                </div>
            </div> -->
            
            
        </div>
        
        
    </div>
</div>
<!-- Delete ALL Records (Danger Zone) - MOVED TO BOTTOM -->
            <!-- <div class="card" style="border: 2px solid #f44336; border-radius: 5px;">
                <div class="card-header" style="background: #ffebee; padding: 10px; border-bottom: 1px solid #f44336;">
                    <h3 style="color: #f44336;"><?php echo __('⚠️ DANGER ZONE - Delete ALL Records'); ?></h3>
                </div>
                <div class="card-body" style="padding: 15px;">
                    <form method="POST" action="<?php echo $current_url; ?>" onsubmit="return confirm('WARNING: This will delete EVERY visitor record in the database! This cannot be undone.');">
                        <input type="hidden" name="action" value="delete_all">
                        <p style="color: #f44336; font-weight: bold;">
                            <?php echo __('This action will permanently delete ALL visitor records from the visitor_count table.'); ?>
                        </p>
                        <div class="form-group">
                            <label><?php echo __('Type "DELETE ALL" to confirm:'); ?></label>
                            <input type="text" name="confirm_all" id="confirm_all" required pattern="DELETE ALL" style="width: 200px;">
                            <button type="submit" class="btn btn-danger" style="margin-left: 10px;"><?php echo __('Delete ALL Records'); ?></button>
                        </div>
                    </form>
                </div>
            </div> -->
<style>
.btn {
    padding: 5px 15px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}
.btn-warning {
    background: #C82333;
    color: white;
}
.btn-danger {
    background: #f44336;
    color: white;
}
.btn:hover {
    opacity: 0.8;
}
.form-control {
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 3px;
}
.card {
    margin-bottom: 20px;
}
.infoBox.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.infoBox.warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}
.infoBox.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
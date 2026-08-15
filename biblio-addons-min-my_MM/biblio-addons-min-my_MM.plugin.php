<?php
/**
 * Plugin Name: <b style="color: #FF8C00;">Biblio-addons-min-my_MM
 * Plugin URI: https://github.com/Ozgurujim/
 * Description: Panel for addons to the cataloguing main page, minimal Burmese version.
 * Version: 1.0.0-my
 * Author: jim richardson
 * Author URI: https://github.com/Ozgurujim/
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();

$plugins->register('bibliography_init', function(){
    echo '<style>
        .floating-panel_MM {
            position: fixed;
            top: 60px;
            right: 480px;
            width: 200px;
            background: white;
            border: 0px solid #2c3e50;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.55);
            z-index: 9999;
            font-family: Arial, sans-serif;
            cursor: move; /* Show move cursor on header */
        }
        .panel-header_MM {
            background: #808080;
            color: white;
            padding: 7px 10px;
            border-radius: 5px 5px 0 0;
            cursor: move;
            user-select: none;
            font-weight: bold;
        }
        .panel-content_MM {
            padding: 12px;
            background: white;
            border-radius: 0 0 5px 5px;
        }
        .floating-panel_MM h3 {
            margin: 0;
            font-size: 16px;
            display: inline-block;
        }
        .floating-panel_MM button {
            /* original background: #3498db; */
			background: #cc7108;
            color: white;
            border: none;
            padding: 8px 12px;
            margin: 3px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }
        .floating-panel_MM button:hover {
            background: #2980b9;
        }
        .floating-panel_MM .close-btn {
            float: right;
            background: #C82333;
            padding: 3px 9px;
            font-size: 12px;
            margin-top: -2px;
            cursor: pointer;
        }
        .floating-panel_MM .close-btn:hover {
            background: #c0392b;
        }
        .floating-panel_MM hr {
            margin: 10px 0;
            border: none;
            border-top: 1px solid #ecf0f1;
        }
    </style>
    
    <div class="floating-panel_MM" id="Helper_MM">
        <div class="panel-header_MM" id="panelHeader_MM">
            <h3>ကိရိယာများ</h3>
            <button class="close-btn" onclick="document.getElementById(\'Helper_MM\').remove()">✖</button>
        </div>
        <div class="panel-content_MM">
            <button onclick="openMLA()">မြန်မာ ပညာရပ်ခေါင်းစဉ်</button>
            

             <hr>
             <button onclick="openQuickNotes()">အမြန်မှတ်စုများ</button>
             
            
        </div>
    </div>
    
    <script>
        // Store window references globally
        var mlaWindow = null;
        var eddcWindow = null;
		var locWindow = null;
        var notesWindow = null;
        var checklistWindow = null;
        
        // Drag and drop functionality
        (function makeDraggable() {
            var panel = document.getElementById("Helper_MM");
            var header = document.getElementById("panelHeader_MM");
            var isDragging = false;
            var offsetX, offsetY;
            
            header.addEventListener("mousedown", function(e) {
                // Only start dragging if clicking on header, not on close button
                if (e.target.classList && e.target.classList.contains("close-btn")) {
                    return;
                }
                
                isDragging = true;
                
                // Get mouse position relative to panel
                offsetX = e.clientX - panel.offsetLeft;
                offsetY = e.clientY - panel.offsetTop;
                
                // Change cursor style
                panel.style.cursor = "grabbing";
                
                e.preventDefault();
            });
            
            document.addEventListener("mousemove", function(e) {
                if (!isDragging) return;
                
                // Calculate new position
                var newX = e.clientX - offsetX;
                var newY = e.clientY - offsetY;
                
                // Keep panel within viewport bounds
                newX = Math.max(0, Math.min(newX, window.innerWidth - panel.offsetWidth));
                newY = Math.max(0, Math.min(newY, window.innerHeight - panel.offsetHeight));
                
                // Apply new position
                panel.style.left = newX + "px";
                panel.style.top = newY + "px";
                panel.style.right = "auto"; // Remove right positioning
            });
            
            document.addEventListener("mouseup", function() {
                isDragging = false;
                panel.style.cursor = "move";
            });
        })();
        
        // MLA Subject Headings - local file with refocus
        function openMLA() {
            if (mlaWindow && !mlaWindow.closed) {
                // Window exists, bring it to front
                mlaWindow.focus();
            } else {
                // Create new window
                mlaWindow = window.open(
                    "../plugins/mla_subject-headings/index.html",
					
                    "mlaHeadings",
                    "width=800,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes"
                );
            }
        }
        
        // Alternative external URL (commented out)
        /*
        function openMLA() {
            window.open(
                "  https://zawaunghtut.github.io/MLA_Subject_Heading/",
                "mlaHeadings",
                "width=800,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes"
            );
        }
        */
        
        // E-DDC - local file with refocus
        function openEDDC() {
            if (eddcWindow && !eddcWindow.closed) {
                // Window exists, bring it to front
                eddcWindow.focus();
            } else {
                // Create new window
                eddcWindow = window.open(
                    "../plugins/eddc/index.html",
                    "eddc",
                    "width=800,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes"
                );
            }
        }
        // LOC - URL file with refocus
        function openLOC() {
            if (locWindow && !locWindow.closed) {
                // Window exists, bring it to front
                locWindow.focus();
            } else {
                // Create new window
                locWindow = window.open(
                    "https://authorities.loc.gov/",
                    "loc",
                    "width=900,height=700,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,screenX=200,screenY=250"
                );
            }
        }
        // Quick Notes with refocus
        function openQuickNotes() {
            if (notesWindow && !notesWindow.closed) {
                // Window exists, bring it to front
                notesWindow.focus();
            } else {
                // Create new window
                notesWindow = window.open(
                    "",
                    "quickNotes",
                    "width=400,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes"
                );
                notesWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head><title>အမြန်မှတ်စုများ</title></head>
                    <body style="font-family: Arial; padding: 15px;">
                        <h3 style="color: #138496;">ပုံကြမ်း</h3>
                        <textarea id="notes" rows="15" cols="40" style="width:100%" 
                            placeholder="ဤနေရာတွင် အမြန်မှတ်စုများ ရေးမှတ်ထားပါ... ဤစာသားကို သင့်ဘရောက်ဆာအတွင်းရှိ ဒေသတွင်းသိုလှောင်မှုတွင် သိမ်းဆည်းထားသည်။ ဘရောက်ဆာကက်ရှ်ကို ရှင်းလင်းခြင်း၊ သီးသန့် သို့မဟုတ် incognito မုဒ်ကို အသုံးပြုခြင်း သို့မဟုတ် ဘရောက်ဆာများကို ပြောင်းလဲခြင်းသည် မသိမ်းဆည်းရသေးသော မှတ်စုများကို အပြီးအပိုင် ဖျက်ပစ်မည်ဖြစ်သော်လည်း သင်အလုပ်လုပ်နေစဉ် ဒေတာများကို ကူးထည့်ရန်နှင့် ကူးယူရန်အတွက် အသုံးဝင်သောနေရာတစ်ခုဖြစ်သည်။"></textarea>
                        <br>
                         <button onclick="saveNotes()">ဘရောက်ဆာ ကက်ရှ်သို့ သိမ်းဆည်းပါ</button>
                         <button onclick="loadNotes()">ကက်ရှ်မှ တင်ရန်</button>
						 
                        <script>
                            function saveNotes() {
                                var notes = document.getElementById("notes").value;
                                localStorage.setItem("bibNotes", notes);
                                alert("မှတ်စုကို သင့်ဘရောက်ဆာကက်ရှ်တွင်သာ သိမ်းဆည်းထားသည်။");
                            }
                            function loadNotes() {
                                var notes = localStorage.getItem("bibNotes");
                                if(notes) document.getElementById("notes").value = notes;
                            }
                            loadNotes();
                        <\/script>
                    </body>
                    </html>
                `);
            }
        }
        
        // Checklist with refocus
        function openChecklist() {
            if (checklistWindow && !checklistWindow.closed) {
                // Window exists, bring it to front
                checklistWindow.focus();
            } else {
                // Create new window
                checklistWindow = window.open(
                    "",
                    "bibValidator",
                    "width=500,height=800,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes"
                );
                checklistWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head><title>စစ်ဆေးရမည့်စာရင်း</title></head>
                    <body style="font-family: Arial; padding: 20px;">
                        <h3>နေ့စဉ်စစ်ဆေးရမည့်စာရင်း</h3>
			<hr>
						<strong style="color: #138496;">စာငှားလုပ်ငန်း</strong>
                        <ul>
                            <li><input type="checkbox"> စာငှားမှတ်တမ်းများကို ပြုပြင်ထိန်းသိမ်းမှု</li>
                            <li><input type="checkbox"> ပြန်အပ်ရမည့်ရက်စွဲ သတိပေးချက်</li>
                            <li><input type="checkbox"> ငှားရမ်းရက်လွန်စာရင်း</li>
                            <li><input type="checkbox"> စနစ် အရန်ကူးယူခြင်း ပြီးစီးပါပြီ။</li>
                        </ul>
			<hr>
						<strong style="color: #138496;">အသင်းဝင်</strong>
						<ul>
                            <li><input type="checkbox"> အသင်းဝင်များ အစီရင်ခံစာ</li>
                            <li><input type="checkbox"> စနစ် အရန်ကူးယူခြင်း ပြီးစီးပါပြီ။</li>
                        </ul>
			<hr>
						<strong style="color: #138496;">စာပြုလုပ်ငန်း</strong>
						<ul>
                            <li><input type="checkbox"> လေဘယ်များ ပုံနှိပ်ခြင်း</li>
                            <li><input type="checkbox"> စာအုပ်ဘားကုဒ်များ ပုံနှိပ်ခြင်း</li>
                            <li><input type="checkbox"> စာကြည့်တိုက် အညွှန်းများ</li>
                            <li><input type="checkbox"> စနစ် အရန်ကူးယူခြင်း ပြီးစီးပါပြီ။</li>
                        </ul>
			<hr>
						<strong style="color: #138496;">စနစ်စီမံခန့်ခွဲမှု</strong>
						<ul>
                            <li><input type="checkbox"> မှတ်ချက်စီမံခန့်ခွဲမှု</li>
                            <li><input type="checkbox"> စနစ်မှတ်တမ်းများကို ပြန်လည်သုံးသပ်ပြီးပါပြီ။</li>
                            <li><input type="checkbox"> စနစ် အရန်ကူးယူခြင်း ပြီးစီးပါပြီ။</li>
                        </ul>
                        <button onclick="window.close()">ပိတ်ရန်</button>
                    </body>
                    </html>
                `);
            }
        }
		
    </script>';
	/**
 * Portions of this code were developed with assistance from
 * DeepSeek AI (https://deepseek.com)
 * Draft Burmese translations aided by Google
 */
});
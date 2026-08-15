<?php
/**
 * Plugin Name: <b style="color: #FF8C00;">Biblio-addons-min
 * Plugin URI: https://github.com/Ozgurujim/
 * Description: Panel for addons to the cataloguing main page, minimal version
 * Version: 1.0.0
 * Author: jim richardson
 * Author URI: https://github.com/Ozgurujim/
 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();

$plugins->register('bibliography_init', function(){
    echo '<style>
        .floating-panel {
            position: fixed;
            top: 87px;
            right: 256px;
            width: 290px;
            background: white;
            border: 1px solid #2c3e50;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.55);
            z-index: 9999;
            font-family: Arial, sans-serif;
            cursor: move; /* Show move cursor on header */
        }
        .panel-header {
            background: #808080;
            color: white;
            padding: 5px 10px;
            border-radius: 5px 5px 0 0;
            cursor: move;
            user-select: none;
            font-weight: bold;
        }
        .panel-content {
            padding: 15px;
            background: white;
            border-radius: 0 0 5px 5px;
        }
        .floating-panel h3 {
            margin: 0;
            font-size: 16px;
            display: inline-block;
        }
        .floating-panel button {
            /* original background: #3498db; */
			background: #cc7108;
            color: white;
            border: none;
            padding: 8px 12px;
            margin: 3px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.2s;
        }
        .floating-panel button:hover {
            background: #2980b9;
        }
        .floating-panel .close-btn {
            float: right;
            background: #C82333;
            padding: 3px 9px;
            font-size: 12px;
            margin-top: -2px;
            cursor: pointer;
        }
        .floating-panel .close-btn:hover {
            background: #c0392b;
        }
        .floating-panel hr {
            margin: 10px 0;
            border: none;
            border-top: 1px solid #ecf0f1;
        }
    </style>
    
    <div class="floating-panel" id="Helper">
        <div class="panel-header" id="panelHeader">
            <h3>Plugins</h3>
            <button class="close-btn" onclick="document.getElementById(\'Helper\').remove()">✖</button>
        </div>
        <div class="panel-content">
            <button onclick="openMLA()">MLA Subject Headings</button>
            
             <hr>
             <button onclick="openQuickNotes()">📝 Quick Notes</button>
             
            
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
            var panel = document.getElementById("Helper");
            var header = document.getElementById("panelHeader");
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
        
        // MLA Subject Headings - local file with refocus (commented out)
        /* function openMLA() {
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
        */ 
        // Alternative external URL 
        
        function openMLA() {
            window.open(
                "https://zawaunghtut.github.io/MLA_Subject_Heading/",
                "citationGuide",
                "width=800,height=600,toolbar=yes,scrollbars=yes,resizable=yes"
            );
        }
        
        
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
                    <head><title>Quick Notes</title></head>
                    <body style="font-family: Arial; padding: 15px;">
                        <h3 style="color: #138496;">Scratchpad</h3>
                        <textarea id="notes" rows="15" cols="40" style="width:100%" 
                            placeholder="Jot quick notes here... This text is saved in local storage within your browser. Clearing browser cache, using private or incognito mode, or switching browsers will permanently delete unsaved notes, but this is a useful place for pasting and copying data as you work."></textarea>
                        <br>
                         <button onclick="saveNotes()">Save to browser cache</button>
                         <button onclick="loadNotes()">Load from browser cache</button>
						 
                        <script>
                            function saveNotes() {
                                var notes = document.getElementById("notes").value;
                                localStorage.setItem("bibNotes", notes);
                                alert("Note saved, to your browser cache ONLY!");
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
                    <head><title>Validate Bibliography Entry</title></head>
                    <body style="font-family: Arial; padding: 20px;">
                        <h3>Daily Checklist</h3>
			<hr>
						<strong style="color: #138496;">Circulation</strong>
                        <ul>
                            <li><input type="checkbox"> Loan History Maintenance completed</li>
                            <li><input type="checkbox"> Due Date Report reviewed</li>
                            <li><input type="checkbox"> Overdues list reviewed</li>
                            <li><input type="checkbox"> System backup completed</li>
                        </ul>
			<hr>
						<strong style="color: #138496;">Membership</strong>
						<ul>
                            <li><input type="checkbox"> Membership report reviewed</li>
                            <li><input type="checkbox"> System backup completed</li>
                        </ul>
			<hr>
						<strong style="color: #138496;">Cataloguing</strong>
						<ul>
                            <li><input type="checkbox"> Label printing reviewed</li>
                            <li><input type="checkbox"> Barcode printing reviewed</li>
                            <li><input type="checkbox"> Biblio Indexes checked</li>
                            <li><input type="checkbox"> System backup completed</li>
                        </ul>
			<hr>
						<strong style="color: #138496;">System Admin</strong>
						<ul>
                            <li><input type="checkbox"> Comment management completed</li>
                            <li><input type="checkbox"> System logs reviewed</li>
                            <li><input type="checkbox"> System backup completed</li>
                        </ul>
                        <button onclick="window.close()">Close</button>
                    </body>
                    </html>
                `);
            }
        }
		
    </script>';
	/**
 * Portions of this code were developed with assistance from
 * DeepSeek AI (https://deepseek.com)
 */
});
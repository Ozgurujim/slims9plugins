# This Python script generates an searchable index of SLiMS documentation pages
# GENERATION SETTINGS:
# - Output file: index.html (default)
# - Search index limit: 5000 characters per page (edit in extract_text_from_html function)
# - Natural sorting: Enabled (numbers sort correctly: 1,2,3...10,11)
# - Window mode default: New Tab (popup available but less reliable)
#
# CUSTOMIZATION OPTIONS:
# - Change header color: Modify .header background in CSS
# - Change accent color: Modify #0066cc in CSS
# - Change search excerpt length: Modify text[:5000] in extract_text_from_html
#
# MAINTENANCE NOTES:
# - Re-run script whenever HTML files are added/changed
# - Backup before major changes: cp generate_search_index.py generate_search_index_backup.py
# - Generated index.html is self-contained (no external dependencies)
#
# TROUBLESHOOTING:
# - If popups don't work: Browser restrictions are normal, use New Tab mode
# - If search misses content: Check that HTML has readable text (not JS-rendered)
# - If file size too large: Reduce the 5000 character limit
# - Released under GNU General Public License version 3.0 (GPLv3) . Created by Gurujim of BSC using DeepSeek. 13/06/2026





import os
import re
import json
from html.parser import HTMLParser

class HTMLTextExtractor(HTMLParser):
    """Extract text content from HTML files, ignoring tags."""
    def __init__(self):
        super().__init__()
        self.text = []
        self.skip_tags = {'script', 'style', 'noscript', 'header', 'footer', 'nav'}
        self.current_skip = 0
    
    def handle_starttag(self, tag, attrs):
        if tag in self.skip_tags:
            self.current_skip += 1
    
    def handle_endtag(self, tag):
        if tag in self.skip_tags:
            self.current_skip -= 1
    
    def handle_data(self, data):
        if self.current_skip == 0:
            text = data.strip()
            if text:
                self.text.append(text)
    
    def get_text(self):
        return ' '.join(self.text)

def extract_text_from_html(filepath):
    """Extract readable text from HTML file."""
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Remove HTML comments
        content = re.sub(r'<!--.*?-->', '', content, flags=re.DOTALL)
        
        # Extract text
        parser = HTMLTextExtractor()
        parser.feed(content)
        text = parser.get_text()
        
        # Normalize whitespace
        text = re.sub(r'\s+', ' ', text)
        return text[:5000]
    except Exception as e:
        print(f"Warning: Could not extract text from {filepath}: {e}")
        return ""

def natural_sort_key(text):
    """Convert string into list of strings and numbers for natural sorting."""
    def convert(c):
        return int(c) if c.isdigit() else c.lower()
    return [convert(c) for c in re.split(r'(\d+)', text)]

def generate_search_index(directory=".", output="index.html"):
    """Generates a collapsible index with full-text search and multiple window options."""
    
    base_path = os.path.abspath(directory)
    
    # Collect all HTML files recursively
    html_files = []
    for root, dirs, files in os.walk(directory):
        if output in files:
            files.remove(output)
        
        for file in files:
            if file.endswith(".html"):
                full_path = os.path.join(root, file)
                rel_path = os.path.relpath(full_path, base_path)
                rel_path = rel_path.replace('\\', '/')
                html_files.append(rel_path)
    
    # Build search index
    print("📇 Building search index...")
    search_index = []
    for file_path in html_files:
        full_path = os.path.join(base_path, file_path)
        text = extract_text_from_html(full_path)
        if text:
            search_index.append({
                'path': file_path,
                'title': os.path.basename(file_path).replace('.html', ''),
                'content': text,
                'folder': os.path.dirname(file_path) or 'Root'
            })
    
    # Group files by directory for tree view
    files_by_dir = {}
    for file_path in html_files:
        dir_name = os.path.dirname(file_path)
        if not dir_name:
            dir_name = "."
        if dir_name not in files_by_dir:
            files_by_dir[dir_name] = []
        files_by_dir[dir_name].append(file_path)
    
    # Sort using natural sorting
    for dir_name in files_by_dir:
        files_by_dir[dir_name].sort(key=natural_sort_key)
    
    sorted_dirs = sorted([d for d in files_by_dir.keys() if d != '.'], key=natural_sort_key)
    
    # Write the index file
    with open(output, "w", encoding="utf-8") as f:
        f.write(f"""<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation Contents with Full-Text Search</title>
    <style>
        * {{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }}
        
        body {{
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            color: #1a1a1a;
            background: #f5f5f5;
            padding: 20px;
        }}
        
        .skip-link {{
            position: absolute;
            top: -40px;
            left: 0;
            background: #0066cc;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 100;
        }}
        
        .skip-link:focus {{
            top: 20px;
        }}
        
        .container {{
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }}
        
        .header {{
            background: #1565c0;
            color: white;
            padding: 32px;
        }}
        
        h1 {{
            font-size: 2rem;
            margin-bottom: 12px;
            font-weight: 700;
        }}
        
        .stats {{
            background: rgba(255,255,255,0.15);
            display: inline-block;
            padding: 6px 16px;
            border-radius: 24px;
            font-size: 0.9rem;
            margin-top: 12px;
        }}
        
        .content {{
            padding: 24px;
        }}
		.disclaimer {{
    margin-top: 12px;
    color: #dc3545;
    font-size: 0.85rem;
    font-weight: 500;
    border-top: 1px solid rgba(255,255,255,0.2);
    padding-top: 12px;
    background: rgba(220, 53, 69, 0.1);
    padding: 10px 15px;
    border-radius: 6px;
}}
        
        .settings-bar {{
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            border: 1px solid #e0e0e0;
        }}
        
        .window-mode-selector {{
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }}
        
        .window-mode-selector label {{
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
        }}
        
        .window-mode-selector label:hover {{
            background: #e9ecef;
        }}
        
        .window-mode-selector input[type="radio"] {{
            margin: 0;
            cursor: pointer;
        }}
        
        .info-badge {{
            background: #e8f0fe;
            color: #0066cc;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 0.8rem;
        }}
        
        .search-tabs {{
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }}
        
        .tab-button {{
            background: none;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            cursor: pointer;
            color: #555;
            font-weight: 500;
            transition: all 0.2s ease;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }}
        
        .tab-button:hover {{
            color: #0066cc;
            background: #f0f7ff;
        }}
        
        .tab-button.active {{
            color: #0066cc;
            border-bottom-color: #0066cc;
        }}
        
        .tab-content {{
            display: none;
        }}
        
        .tab-content.active {{
            display: block;
        }}
        
        .search-box {{
            margin-bottom: 24px;
        }}
        
        .search-box label {{
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }}
        
        .search-box input {{
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #757575;
            border-radius: 8px;
            font-size: 1rem;
            line-height: 1.5;
            background: white;
            color: #1a1a1a;
        }}
        
        .search-box input:focus {{
            border-color: #0066cc;
            outline: 3px solid rgba(0,102,204,0.1);
        }}
        
        .search-results {{
            margin-top: 20px;
        }}
        
        .result-item {{
            padding: 16px;
            margin: 12px 0;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }}
        
        .result-item:hover {{
            background: #f8f9fa;
            border-color: #0066cc;
        }}
        
        .result-title {{
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }}
        
        .result-title a {{
            color: #0066cc;
            text-decoration: none;
            cursor: pointer;
        }}
        
        .result-title a:hover {{
            text-decoration: underline;
        }}
        
        .result-folder {{
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 8px;
        }}
        
        .result-excerpt {{
            font-size: 0.9rem;
            color: #444;
            line-height: 1.5;
        }}
        
        .result-excerpt mark {{
            background: #ffeb3b;
            padding: 0 2px;
            border-radius: 3px;
        }}
        
        .search-stats {{
            padding: 10px;
            background: #f0f7ff;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #0066cc;
        }}
        
        .folder-item {{
            margin: 12px 0;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #e0e0e0;
        }}
        
        .folder-header {{
            cursor: pointer;
            padding: 14px 16px;
            background: #f8f9fa;
            border: none;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
        }}
        
        .folder-header:hover {{
            background: #e9ecef;
        }}
        
        .folder-toggle {{
            font-size: 1.2rem;
            font-weight: bold;
            color: #0066cc;
            width: 24px;
            display: inline-block;
        }}
        
        .folder-icon {{
            font-size: 1.2rem;
        }}
        
        .folder-name {{
            flex: 1;
        }}
        
        .folder-count {{
            background: #e0e0e0;
            color: #1a1a1a;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }}
        
        .files-container {{
            margin-left: 48px;
            padding: 8px 0;
            display: none;
            border-top: 1px solid #e0e0e0;
        }}
        
        .files-container.open {{
            display: block;
        }}
        
        .file-list {{
            list-style: none;
            padding: 0;
        }}
        
        .file-list li {{
            margin: 4px 0;
        }}
        
        .doc-link {{
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            text-decoration: none;
            color: #0066cc;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            border-radius: 4px;
            cursor: pointer;
        }}
        
        .doc-link:hover {{
            background: #f0f7ff;
            border-left-color: #0066cc;
            text-decoration: underline;
        }}
        
        .file-icon {{
            font-size: 1.1rem;
            flex-shrink: 0;
        }}
        
        .file-name {{
            flex: 1;
        }}
        
        .external-indicator {{
            font-size: 0.7rem;
            color: #666;
            margin-left: 8px;
            flex-shrink: 0;
        }}
        
        .root-folder {{
            background: #f0f7ff;
            border-color: #0066cc;
        }}
        
        .root-folder .folder-header {{
            background: #e8f0fe;
        }}
        
        .no-results {{
            text-align: center;
            padding: 48px;
            color: #555555;
            border: 2px dashed #cccccc;
            border-radius: 8px;
            margin: 24px 0;
        }}
        
        .sr-only {{
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }}
        
        @media (max-width: 768px) {{
            body {{
                padding: 10px;
            }}
            .header {{
                padding: 20px;
            }}
            h1 {{
                font-size: 1.5rem;
            }}
            .files-container {{
                margin-left: 24px;
            }}
            .settings-bar {{
                flex-direction: column;
            }}
        }}
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <div class="container">
        <div class="header">
            <h1>📚 SLiMS 9.7.2 Documentation with Full-Text Search</h1>
            <div class="stats" id="stats" aria-live="polite">Loading...</div>
        </div>
        <div class="disclaimer">
           ⚠️ This documentation may contain errors, or be outdated. It is not official or definitive, and should be used with caution.
        </div>
        <div class="content" id="main-content">
            <div class="settings-bar">
                <div class="window-mode-selector" role="group" aria-label="Window opening preference">
                    <span style="font-weight: 500;">📂 Open links in:</span>
                    <label>
                        <input type="radio" name="windowMode" value="popup"> 
                        🪟 Popup Window
                    </label>
                    <label>
                        <input type="radio" name="windowMode" value="tab" checked> 
                        📑 New Tab (Recommended)
                    </label>
                    <label>
                        <input type="radio" name="windowMode" value="same"> 
                        📄 Same Window
                    </label>
                </div>
                <div class="info-badge">
                    💡 New Tab mode is most reliable across all browsers
                </div>
            </div>
            
            <div class="search-tabs">
                <button class="tab-button active" onclick="switchTab('browse')">📁 Browse</button>
                <button class="tab-button" onclick="switchTab('search')">🔍 Full-Text Search</button>
            </div>
            
            <div id="browse-tab" class="tab-content active">
                <nav aria-label="Documentation table of contents">
                    <div id="tree"></div>
                </nav>
            </div>
            
            <div id="search-tab" class="tab-content">
                <div class="search-box">
                    <label for="searchInput">Search in documentation content</label>
                    <input type="text" id="searchInput" placeholder="Search across all documentation content..." autocomplete="off">
                </div>
                <div id="searchResults"></div>
            </div>
        </div>
    </div>
    
    <div class="sr-only" id="liveRegion" aria-live="polite"></div>
    
    <script>
        const searchIndex = {json.dumps(search_index, ensure_ascii=False)};
        const filesByDir = {json.dumps(files_by_dir)};
        const sortedDirs = {json.dumps(sorted_dirs)};
        
        let totalFiles = searchIndex.length;
        
        function getWindowMode() {{
            const saved = localStorage.getItem('docWindowMode');
            if (saved === 'popup') return 'popup';
            if (saved === 'same') return 'same';
            return 'tab'; // default to tab (most reliable)
        }}
        
        function setWindowMode(mode) {{
            localStorage.setItem('docWindowMode', mode);
            const modeNames = {{'popup': 'popup window', 'tab': 'new tab', 'same': 'same window'}};
            announce(`Window mode changed to ${{modeNames[mode]}}`);
            updateAllLinks();
        }}
        
        function openDocumentation(url, event) {{
            const mode = getWindowMode();
            
            if (mode === 'tab') {{
                window.open(url, '_blank', 'noopener,noreferrer');
            }} else if (mode === 'same') {{
                window.location.href = url;
            }} else if (mode === 'popup') {{
                // Firefox-friendly popup - simple, no tracking
                const width = 900;
                const height = 700;
                const left = (window.screen.width - width) / 2;
                const top = (window.screen.height - height) / 2;
                const features = `width=${{width}},height=${{height}},left=${{left}},top=${{top}},resizable=yes,scrollbars=yes,toolbar=yes,location=yes`;
                window.open(url, '_blank', features);
            }}
            
            if (event) {{
                event.preventDefault();
            }}
            return false;
        }}
        
        function announce(message) {{
            const liveRegion = document.getElementById('liveRegion');
            liveRegion.textContent = message;
            setTimeout(() => {{ liveRegion.textContent = ''; }}, 2000);
        }}
        
        function switchTab(tab) {{
            document.getElementById('browse-tab').classList.remove('active');
            document.getElementById('search-tab').classList.remove('active');
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            
            if (tab === 'browse') {{
                document.getElementById('browse-tab').classList.add('active');
                document.querySelector('.tab-button').classList.add('active');
            }} else {{
                document.getElementById('search-tab').classList.add('active');
                document.querySelectorAll('.tab-button')[1].classList.add('active');
                document.getElementById('searchInput').focus();
            }}
        }}
        
        function performSearch() {{
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const resultsContainer = document.getElementById('searchResults');
            
            if (!query) {{
                resultsContainer.innerHTML = '<div class="search-stats">🔍 Enter search terms to find content across all documentation</div>';
                return;
            }}
            
            const results = [];
            for (const doc of searchIndex) {{
                const titleMatch = doc.title.toLowerCase().includes(query);
                const contentMatch = doc.content.toLowerCase().includes(query);
                
                if (titleMatch || contentMatch) {{
                    let excerpt = '';
                    if (contentMatch) {{
                        const content = doc.content;
                        const index = content.toLowerCase().indexOf(query);
                        const start = Math.max(0, index - 100);
                        const end = Math.min(content.length, index + query.length + 100);
                        excerpt = (start > 0 ? '...' : '') + 
                                 content.substring(start, end).replace(new RegExp(`(${{query}})`, 'gi'), '<mark>$1</mark>') + 
                                 (end < content.length ? '...' : '');
                    }}
                    results.push({{...doc, score: (titleMatch ? 2 : 0) + (contentMatch ? 1 : 0), excerpt}});
                }}
            }}
            
            results.sort((a, b) => b.score - a.score);
            
            if (results.length === 0) {{
                resultsContainer.innerHTML = `<div class="no-results">🔍 No results found for "${{query}}"</div>`;
            }} else {{
                let html = `<div class="search-stats">🔍 Found ${{results.length}} result${{results.length !== 1 ? 's' : ''}} for "${{query}}"</div>`;
                for (const result of results) {{
                    html += `
                        <div class="result-item">
                            <div class="result-title">
                                <a href="#" onclick="openDocumentation('${{escapeHtml(result.path)}}', event); return false;">${{escapeHtml(result.title)}}</a>
                            </div>
                            <div class="result-folder">📁 ${{escapeHtml(result.folder)}}</div>
                            ${{result.excerpt ? `<div class="result-excerpt">${{result.excerpt}}</div>` : ''}}
                        </div>
                    `;
                }}
                resultsContainer.innerHTML = html;
            }}
        }}
        
        function escapeHtml(text) {{
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }}
        
        let searchTimeout;
        function debouncedSearch() {{
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        }}
        
        function generateFolderHTML(folderName, files, isRoot = false) {{
            const folderId = 'folder_' + Math.random().toString(36).substr(2, 9);
            const fileCount = files.length;
            const displayName = folderName === '.' ? 'Root' : folderName.replace(/\\\\/g, ' / ').replace(/\\//g, ' / ');
            const mode = getWindowMode();
            const indicator = mode === 'popup' ? '🪟' : (mode === 'tab' ? '🔗' : '');
            const hint = mode === 'popup' ? 'opens in popup window' : (mode === 'tab' ? 'opens in new tab' : 'opens in same window');
            
            let html = `<div class="folder-item ${{isRoot ? 'root-folder' : ''}}">
                            <button class="folder-header" onclick="toggleFolder('${{folderId}}')" aria-expanded="false" id="btn_${{folderId}}">
                                <span class="folder-toggle">▶</span>
                                <span class="folder-icon">📁</span>
                                <span class="folder-name">${{escapeHtml(displayName)}}</span>
                                <span class="folder-count">${{fileCount}}</span>
                            </button>
                            <div class="files-container" id="${{folderId}}">
                                <ul class="file-list">`;
            
            for (const filePath of files) {{
                const fileName = filePath.split('/').pop().replace('.html', '');
                let icon = "📄";
                if (fileName.toLowerCase().includes('readme')) icon = "📖";
                else if (fileName.toLowerCase().includes('api')) icon = "🔌";
                else if (fileName.toLowerCase().includes('guide')) icon = "📘";
                
                html += `<li>
                            <a href="#" class="doc-link" onclick="openDocumentation('${{escapeHtml(filePath)}}', event); return false;">
                                <span class="file-icon">${{icon}}</span>
                                <span class="file-name">${{escapeHtml(fileName)}}</span>
                                ${{indicator ? `<span class="external-indicator">${{indicator}}</span>` : ''}}
                                <span class="sr-only">${{fileName}} (${{hint}})</span>
                            </a>
                        </li>`;
            }}
            
            html += `</ul></div></div>`;
            return html;
        }}
        
        function toggleFolder(folderId) {{
            const folder = document.getElementById(folderId);
            const toggle = document.getElementById('btn_' + folderId);
            
            if (folder.classList.contains('open')) {{
                folder.classList.remove('open');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
                const toggleIcon = toggle?.querySelector('.folder-toggle');
                if (toggleIcon) toggleIcon.textContent = '▶';
            }} else {{
                folder.classList.add('open');
                if (toggle) toggle.setAttribute('aria-expanded', 'true');
                const toggleIcon = toggle?.querySelector('.folder-toggle');
                if (toggleIcon) toggleIcon.textContent = '▼';
            }}
            saveFolderState();
        }}
        
        function buildTree() {{
            const treeContainer = document.getElementById('tree');
            let html = '';
            
            if (filesByDir['.']) {{
                html += generateFolderHTML('.', filesByDir['.'], true);
            }}
            for (const dirName of sortedDirs) {{
                if (filesByDir[dirName]) {{
                    html += generateFolderHTML(dirName, filesByDir[dirName], false);
                }}
            }}
            
            treeContainer.innerHTML = html;
            
            // Auto-expand root
            const rootContainer = document.querySelector('.root-folder .files-container');
            const rootToggle = document.querySelector('.root-folder .folder-header');
            if (rootContainer && rootToggle) {{
                rootContainer.classList.add('open');
                rootToggle.setAttribute('aria-expanded', 'true');
                const toggleIcon = rootToggle.querySelector('.folder-toggle');
                if (toggleIcon) toggleIcon.textContent = '▼';
            }}
            loadFolderState();
        }}
        
        function updateAllLinks() {{
            const mode = getWindowMode();
            const indicator = mode === 'popup' ? '🪟' : (mode === 'tab' ? '🔗' : '');
            const hint = mode === 'popup' ? 'opens in popup window' : (mode === 'tab' ? 'opens in new tab' : 'opens in same window');
            
            document.querySelectorAll('.external-indicator').forEach(el => {{
                if (indicator) {{
                    el.textContent = indicator;
                    el.style.display = '';
                }} else {{
                    el.style.display = 'none';
                }}
            }});
            
            document.querySelectorAll('.file-list li .sr-only').forEach(el => {{
                const text = el.textContent.split('(')[0].trim();
                el.textContent = `${{text}} (${{hint}})`;
            }});
        }}
        
        function saveFolderState() {{
            const folders = document.querySelectorAll('.files-container');
            const state = {{}};
            folders.forEach(folder => {{ state[folder.id] = folder.classList.contains('open'); }});
            localStorage.setItem('folderState', JSON.stringify(state));
        }}
        
        function loadFolderState() {{
            const savedState = localStorage.getItem('folderState');
            if (savedState) {{
                const state = JSON.parse(savedState);
                for (const [id, isOpen] of Object.entries(state)) {{
                    const folder = document.getElementById(id);
                    const button = document.getElementById('btn_' + id);
                    if (folder && button && isOpen) {{
                        folder.classList.add('open');
                        button.setAttribute('aria-expanded', 'true');
                        const toggleIcon = button.querySelector('.folder-toggle');
                        if (toggleIcon) toggleIcon.textContent = '▼';
                    }}
                }}
            }}
        }}
        
        // Initialize
		const generationDate = new Date().toLocaleDateString();
        document.getElementById('stats').textContent = `📊 ${{totalFiles}} documentation pages with full-text search | Generated on ${{generationDate}} by GuruJim, BSC.`;
        
        // Set up radio buttons
        const savedMode = getWindowMode();
        document.querySelectorAll('input[name="windowMode"]').forEach(radio => {{
            if (radio.value === savedMode) radio.checked = true;
            radio.addEventListener('change', function() {{
                if (this.checked) setWindowMode(this.value);
            }});
        }});
        
        buildTree();
        
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.addEventListener('input', debouncedSearch);
        
        // Keyboard support
        document.addEventListener('keydown', function(e) {{
            if (e.target.classList?.contains('folder-header') && (e.key === 'Enter' || e.key === ' ')) {{
                e.preventDefault();
                e.target.click();
            }}
        }});
        
        window.switchTab = switchTab;
        window.toggleFolder = toggleFolder;
        window.openDocumentation = openDocumentation;
    </script>
	
</body>
</html>
""")
    
    print(f"✅ Full-text search index generated: {output}")
    print(f"   📊 Indexed {len(search_index)} HTML files")
    print(f"   💾 Search index size: {len(json.dumps(search_index)) / 1024:.1f} KB")
    print(f"   📑 New Tab mode is now the default (most reliable)")
    print(f"   🪟 Popup mode available but may have browser restrictions")
    
    return len(search_index)

if __name__ == "__main__":
    import sys
    if len(sys.argv) > 1:
        generate_search_index(sys.argv[1])
    else:
        generate_search_index()
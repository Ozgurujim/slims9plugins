This documentation is built upon the available official documentation.
As the original documentation for SLiMS 9 was largely written in Markdown, that approach has been continued. PandocGUI was used to convert the Markdown files to HTML. 
Because the documentation is designed to be standalone, this plugin retains all that structure, including the python script to regenerate the indexing for any updated or altered pages. Accordingly, *launch-SLIMSdocs.html* is used to open a separate window for displaying the main page: *index.html*.
You should secure your installation so that the python script cannot be executed by unauthorised persons, particularly if your server is exposed to the internet. The script SHOULD be executed after changes are made to any of the documentation pages, by keeping the script in the same directory and navigatimg to the directory in your CLI and executing the command  "python generate_index.py" . [ Python is available from Python.org if you don't have it installed ]

gurujim | BSC | 15/08/2026

All this is released under GNU GENERAL PUBLIC LICENSE Version 3, a copy of which is included.

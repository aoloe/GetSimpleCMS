#TODO

- finish to implement `GS_storage` and use it instead of GS global function for read and write
- always use the `GS_Message` as a singleton

#Wanted features

- cross plugins communications (multiuser should manage the rights to access the content of the installed plugins).
- allow the manual editing of html content, without using the online editor (content should not be stored in the xml file)
- upload of images in the image widget of the editor.
- "intelligent" link to images and other library items (the http reference should be created when rendering the page, not when saving it)
- separate the content from the backend in the administration tabs.
- a common ajax way of saving the content (no reload of the page if it is not necessary... at least for page editing).
- list of css styles in the editor (H1, H2, p, pre, p.comment, ...).
- list of pages should not be in creation order (menu, alphabetical, last changed are all better).
- ask for the old password when prompting for a new one.
- add to the files list the same filter as in the page list.
- a way to control the height of the editing text area for selected pages
- for most of the storage move away from xml and use json (if it's indeed better...)

#Proposed features

- a plugin to manage content fields (for lists)
- a plugin to manage lists
- a plugin to smooth the transition to a more modern PHP code
  - a small and simple template engine.
- for most things get away from xml and use yaml + json (if we don't validate the xml, there is no reason to use it!)

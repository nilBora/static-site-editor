 var Backend = {
    init: function() {
        
    },
    
 }
 
 jQuery(function() {
    Backend.init(); 
 });
 
 jQuery(function() {
        jQuery('.js-open-dir').on('click', function(e) {
            e.preventDefault();
            
            var $this = jQuery(this);
            var path = $this.data('path');
            var name = $this.data('name');
            var href = $this.attr('href');
            
            jQuery.post('/nbeditor/save/filemanager/', {'path': path, 'name': name}, function(data) {
                window.location.reload();
            })
        });
        
        jQuery('.js-open-history-dir').on('click', function(e) {
            e.preventDefault();
            
            var $this = jQuery(this);
            var path = $this.data('path');
            var name = $this.data('name');
            var href = $this.attr('href');
            
            jQuery.post('/nbeditor/save/history/', {'path': path, 'name': name}, function(data) {
                window.location.reload();
            })
        });
        
        
        jQuery('.js-file-edit').on('click', function(e) {
            e.preventDefault();
            var $this = jQuery(this);
            var name = $this.data('name');
            var path = $this.data('path');
            var href = $this.attr('href');
            
            jQuery.post('/nbeditor/content/save/file/', {'path': path, 'name': name}, function(data) {
                window.location.href = href;
            })
            
        })
        
        
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/twilight");
        
        var currentMode = ace.require("ace/mode/"+baseCurrentMode).Mode;
        editor.session.setMode(new currentMode());

        
    });

var nnCore = {
    
    lastElement: false,
    selectors: {
        tag: '.nneditor-tag',
        tagChange: '.nneditor-tag-change'
    },
    
    onInit: function() {
        
        this.onCheckjQuery();

        this.onSaveContent();
        
        this.appendContentEditable();
        this.appnedChangeContentEditable();
        
        this.appendUserTags();
        
        this.changeImage();
    },
    
    onCheckjQuery: function() {
        if (!window.jQuery) {
            console.log("Include JS");
        }
    },
    
    appnedChangeContentEditable: function() {
        jQuery(this.selectors.tag).on('input', function() {
            jQuery(this).addClass('nneditor-tag-change');
            nnCore.lastElement = jQuery(this);
        });
    },
    
    appendContentEditable: function() {
        ALLOW_TAGS.map(function(tag, i) {
            jQuery(tag).attr('contenteditable', true);
            jQuery(tag).addClass('nneditor-tag');
        })
        
    },
    
    changeImage: function() {
        jQuery('.nneditor-tag').on('click', function(e) {
            if (jQuery(this).is('img')) {
                console.log('IMG');
            }
        })
    },
    
    appendUserTags: function() {
       jQuery('#h1').on('click', function(e) {
           e.preventDefault();
           nnCore.appendUserTag('h1');
       });
       
       jQuery('#h2').on('click', function(e) {
           e.preventDefault();
           nnCore.appendUserTag('h2');
       });
    },
    
    appendUserTag: function(tag) {
        var content = nnCore.lastElement.html();
        
        if (nnCore.hasUserTag()) {
            nnCore.lastElement.first().children().contents().unwrap();
        }
        nnCore.lastElement.addClass('nnEditor-user-tag');
        nnCore.lastElement.wrapInner(document.createElement(tag));
    },
    
    hasUserTag: function() {
        return nnCore.lastElement.hasClass('nnEditor-user-tag');
    },
    
    onSaveContent: function() {
        jQuery('#nn_button_save').click(function () {
            if (confirm("You want to save the file?")) { 
                var key, content = {};
                var $this = jQuery(this);

                jQuery('*').removeAttr('contenteditable');
                
                jQuery('.nneditor-tag-change').map(function(i) {
                    key = jQuery(this).data('nneditor');
                    content[key] = jQuery(this).html();
                });

                var contentJson = JSON.stringify(content);

                jQuery.post( "/nnEditor/index.php", {
                    'save': 1,
                    'url': jQuery('body').data('nneditor-url'),
                    'content': contentJson,
                    'action': ''
                }, function( data ) {
                     $this.parent().detach();
                    //window.location.reload();
                });
            }
        });
    }
}

nnCore.onInit();
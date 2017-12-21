var nilContentEditable = {
    nilContent: {},
    onInit: function() {

        this.onCheckjQuery();
        this.doContentEditableTrue();
        this.doAppendControllButton();
        this.doShearchContentEditableTrue();
        this.onSaveContent();
        
        this.appendContentEditable();
        
        this.appnedChangeContentEditable();
    },
    
    onCheckjQuery: function() {
        if (!window.jQuery) {
            console.log("Include JS");
        }
    },
    
    appnedChangeContentEditable: function() {
        jQuery('.nneditor-tag').on('input', function() {
            jQuery(this).addClass('nneditor-tag-change'); 
        });
    },
    
    appendContentEditable: function() {
        var tags = ['p', 'h1', 'li'];
        tags.map(function(tag, i) {
            jQuery(tag).attr('contenteditable', true);
            jQuery(tag).addClass('nneditor-tag');
        })
        
    },
    
    
    //modifide
    doContentEditableTrue: function() {
        jQuery("p[class^='nil-edit-content-']").each(function() {
            jQuery(this).attr('contenteditable', true);
        })
    },

    doAppendControllButton: function() {
        jQuery('body').append('<div id="nil_system_info"><a id="nil_button_save">Save</a></div>');
        jQuery('#nil_system_info').css(
            {
                'position': 'fixed',
                'bottom': '0px',
                'left': '0px',
                'width': '100%',
                'height': '50px',
                'margin': '0',
                'padding': '5px',
                'background': '#87CEEB',
                'border': '1px solid #B3B4BD',
                'font': '11px verdana',
                'z-index': '9999',
                'opacity': '0.9',
                'filter': 'alpha(Opacity=90)',
                /*'border-radius': '8px'*/
            }
        );

        jQuery('#nil_button_save').css(
            {
                'cursor': 'pointer',
                'color': '#00008B',
            }
        );
    },

    doShearchContentEditableTrue: function() {
        var key;
        jQuery("[contenteditable=true]").blur(function() {
            key = jQuery(this).data('nnEditor');
            nilContentEditable.nilContent[key] = jQuery(this).html();
        });
    },

    onSaveContent: function() {
        jQuery('#nil_button_save').click(function () {
            if (confirm("You want to save the file?")) { 
                var key, content = {};
                var $this = jQuery(this);
                
/*
                jQuery("[contenteditable=true]").map(function() {
                    key = jQuery(this).data('nneditor');
                    content[key] = jQuery(this).html();
                });
*/
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
                    //'nilContent': nilContent
                }, function( data ) {
                     $this.parent().detach();
                     //jQuery('*').removeAttr('contenteditable');
                    
                    //window.location.reload();
                });
            }
        });
    }
}

nilContentEditable.onInit();
var nnCore = {
    
    selectors: {
        tag: '.nneditor-tag',
        tagChange: '.nneditor-tag-change'
    },
    
    onInit: function() {
        
        
        this.onCheckjQuery();
        this.doAppendControllButton();
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
        jQuery(this.selectors.tag).on('input', function() {
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

    onSaveContent: function() {
        jQuery('#nil_button_save').click(function () {
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
                }, function( data ) {
                     $this.parent().detach();
                    //window.location.reload();
                });
            }
        });
    }
}

nnCore.onInit();

var nilContentEditable = {
    nilContent: {},
    onInit: function() {

        this.onCheckjQuery();
        this.doContentEditableTrue();
        this.doAppendControllButton();
        this.doShearchContentEditableTrue();
        this.onSaveContent();
    },

    onCheckjQuery: function() {
        if (!window.jQuery) {
            console.log("Include JS");
        }
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
                'bottom': '0',
                'right': '0',
                'width': '300px',
                'height': '20px',
                'margin': '0',
                'padding': '5px',
                'background': '#F9F9F9',
                'border': '1px solid #B3B4BD',
                'font': '11px verdana',
                'z-index': '9999',
            }
        );

        jQuery('#nil_button_save').css(
            {
                'cursor': 'pointer',
                'color': 'red',

            }
        );
    },

    doShearchContentEditableTrue: function() {
        var key;
        jQuery("[contenteditable=true]").blur(function() {
            key = jQuery(this).data('nilcontent');
            nilContentEditable.nilContent[key] = jQuery(this).html();
        });
    },

    onSaveContent: function() {
        jQuery('#nil_button_save').click(function () {
            if (confirm("You want to save the file?")) {
                jQuery(this).html('');
                jQuery('body').attr('contenteditable', false);
                var content = JSON.stringify(nilContentEditable.nilContent);

                jQuery.post( "/backend/cms.php", {
                    'save': 1,
                    'url': jQuery('body').data('nilurl'),
                    'content': content,
                    //'nilContent': nilContent
                }, function( data ) {
                    //window.location.reload();
                });
            }
        });
    }
}

nilContentEditable.onInit();

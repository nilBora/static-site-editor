var nnCore = {
    
    lastElement: false,
    lastElementSelection: false,
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
        
        this.doUserFunc();
    },
    
    onCheckjQuery: function() {
        if (!window.jQuery) {
            console.log("Include JS");
        }
    },
    
    doUserFunc: function() {
        jQuery('.nneditor-tag').on('click', function (e) {
            console.log('Click!!!');
        })
    },
    
    appnedChangeContentEditable: function() {
        jQuery(this.selectors.tag).on('focus', function() {
            jQuery(this).addClass('nneditor-tag-change');
            nnCore.lastElement = jQuery(this);
        });
        jQuery(this.selectors.tag).dblclick(function() {
            
            jQuery(this).addClass('nneditor-tag-change');
            nnCore.lastElement = jQuery(this);
            nnCore.lastElementSelection = window.getSelection();
        });
        
    },
    
    appendContentEditable: function() {
        ALLOW_TAGS.map(function(tag, i) {
            jQuery(tag).attr('contenteditable', true);
            jQuery(tag).addClass('nneditor-tag');
        });
        //XXX: KOSTIL FIX ME
        jQuery('.nn-custom').removeAttr('contenteditable');
        jQuery('.nn-custom').removeClass('nneditor-tag');
    },
    
    changeImage: function() {
        jQuery('.nneditor-tag').on('click', function(e) {
            if (jQuery(this).is('img')) {
/*
                jQuery(this).addClass('nneditor-tag-img');
                var popup = '<input class="nneditor-tag-img-input" type="text" value="'+jQuery(this).attr('src')+'">';
                jQuery(this).after(popup);
                console.log('IMG='+jQuery(this).attr('src'));
*/
            }
        });
        
        /* Поставить проверку есть ли IMG в ALLOW_TAGS*/
        jQuery('img').blur(function() {
            if (jQuery(this).hasClass('nneditor-tag-img-active')) {
                return true;
            }
            if (jQuery(this).next().hasClass('nneditor-tag-img-input')) {
                var imgPath = jQuery(this).next().val();
                jQuery(this).next().detach();
                jQuery(this).attr('src', imgPath);
                
            }
        });
        
        jQuery('img').focusin(function() {
            jQuery(this).addClass('nneditor-tag-img-active');
            var popup = '<input class="nneditor-tag-img-input" type="text" value="'+jQuery(this).attr('src')+'">';
            jQuery(this).after(popup);
        });
        
        jQuery('body').on('blur', '.nneditor-tag-img-input', function() {
            var value = jQuery(this).val();
            jQuery(this).prev('img').attr('src', value);
            console.log(jQuery(this).prev('img').attr('src'));
            jQuery(this).detach();
        })
        
        
        jQuery('.nneditor-tag-img-input').on('change', function() {

            var value = jQuery(this).val();
            console.log(value);
            jQuery(this).prev().attr('src', value);
            console.log(jQuery(this).prev().attr('src'));
        })
        
        jQuery("*").focusin(function() {
/*
            if (!jQuery(this).hasClass('nneditor-tag-img')) {
                jQuery('.nneditor-tag-img-input').detach();
            }
*/
           
        })
    },
    
    appendUserTags: function() {
        jQuery('.nn-custom-button').on('click', function(e) {
            e.preventDefault();
            
            nnCore.appendUserTag(jQuery(this));
        });
    },
    
    appendUserTag: function($this) {
        
        var tag = $this.data('nn-tag');
       
        
        var content = nnCore.lastElement.html();
        
        if (nnCore.hasUserTag()) {
            //nnCore.lastElement.first().children().contents().unwrap();
        }
        nnCore.lastElement.addClass('nnEditor-user-tag');
        
        if (tag == 'textDecoration') {
            //nnCore.lastElement.focus();
            
            var range, sel, command, commandParam;
            if (window.getSelection) {
                // Non-IE case
                sel = window.getSelection();
                if (sel.getRangeAt) {
                    range = sel.getRangeAt(0);
                }
                document.designMode = "on";
                if (range) {
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
                
                if ($this[0].hasAttribute('data-nn-command')) {
                    command = $this.data('nn-command');
                }
                
                commandParam = null;
                if ($this[0].hasAttribute('data-nn-param')) {
                    commandParam = $this.data('nn-param');
                }
                
                
                document.execCommand(command, false, commandParam);
                
                var newNode = window.getSelection().focusNode.parentNode;
                jQuery(newNode).addClass("nnEditor-user-tag");
                jQuery(newNode).attr('contenteditable', true);
                
                
                document.designMode = "off";
            } else if (
                document.selection && document.selection.createRange &&
                document.selection.type != "None"
            ) {
                // IE case
                range = document.selection.createRange();
                range.execCommand(command, false, null);
            }
        } else if (tag == 'ul' || tag == 'ol' || tag == 'dl') {
            var contentBr = content.split('\n');
            nnCore.lastElement.after(this.createList(contentBr, tag));
        } else {
            nnCore.lastElement.wrapInner(document.createElement(tag));   
        }
        
    },
    
    getCommandByTag: function(tag) {
        var command = false;
        if (tag == 'i') {
            command = 'italic';
        } else if (tag == 'strong') {
            command = 'bold';
        } else if (tag == 'u') {
            command = 'underline';
        }
        
        return command;
    },
    
    createList: function(spacecrafts, type = 'ul'){
        /* Херня не сохраняет из за data-nneditor */
        var listView=document.createElement(type);
        
        var att = document.createAttribute('class');
        att.value = 'nneditor-tag nneditor-tag-change';
        listView.setAttributeNode(att);
            
        att = document.createAttribute('contenteditable');
        att.value = true;
        listView.setAttributeNode(att);
            
        att = document.createAttribute('data-nneditor');
        var count = 1;
        att.value = 'user'+count;
        listView.setAttributeNode(att);
        
        
        for (var i=0; i<spacecrafts.length; i++) {
            var listViewItem=document.createElement('li');
            
            listViewItem.appendChild(document.createTextNode(spacecrafts[i]));
            listView.appendChild(listViewItem);
        }
        
        return listView;
    },
    
    hasUserTag: function() {
        return nnCore.lastElement.hasClass('nnEditor-user-tag');
    },
    
    onSaveContent: function() {
        jQuery('#nn_button_save').click(function () {
            if (confirm("You want to save the file?")) {                 
                nnCore.doClearCoreContent();
                
                var body = jQuery('body').html();
                jQuery.post( "/nnEditor/index.php", {
                    'save': 1,
                    'url': jQuery('body').data('nneditor-url'),
                    'body': body,
                    'action': ''
                }, function( data ) {
                     //$this.parent().detach();
                    //window.location.reload();
                });
            }
        });
    },
    
    doClearCoreContent: function() {
        jQuery('#nn_system_info').detach();
        
        jQuery('*').removeAttr('contenteditable');
        jQuery('*').removeAttr('data-nneditor');
        
        jQuery('*').removeClass('nneditor-tag');
        jQuery('*').removeClass('nnEditor-user-tag');
    }
}

nnCore.onInit();
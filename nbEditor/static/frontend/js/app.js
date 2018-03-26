var ALLOW_TAGS = ['p','h1', 'ul', 'img'];
var nnCore = {
    staticPath: '/nbEditor/static/frontend/',
    lastElement: false,
    selectors: {
        tag: '.nneditor-tag',
        tagChange: '.nneditor-tag-change'
    },
    
    onInit: function() {
        
        //this.initJS();
        
        this.onSaveContent();
        
        this.appendContentEditable();
        this.appnedChangeContentEditable();
        
        this.appendUserTags();
        
        this.changeImage();
        
        this.doUserFunc();
        
        this.onInitColorPicker();
    },
    
    initJS: function() {
        
        if (!window.jQuery) {
            jQuery('head').append('<script type="text/javascript" src="/js/jquery.js"></script>');
        }
        
        this.initCss();
        
        jQuery.post( "/nbEditor/index.php?url=/load/panel/", {}, function(data) {
            jQuery('.nn-editor-content').html(data);
           
        });
        
         nnCore.onInit();

    },
    
    initCss: function() {
        
        var links = [
            'css/contenteditable.css',
            'plugins/colorpicker/css/colorpicker.css',
            'plugins/colorpicker/css/layout.css',
            'components/bootstrap/dist/css/bootstrap.min.css',
            'css/panel.css',
            'components/font-awesome/css/font-awesome.css'
        ];
        
        links.map(function (name) {
            jQuery('head').append(
                '<link type="text/css" rel="stylesheet" href="'+nnCore.staticPath+name+'"></link>'
            );
        })
    },
        
    i: function(msg) {
        console.info(msg);
    },
    
    e: function(msg) {
        console.warn(msg);
    },
    
    getParentObj: function(selector) {
        return jQuery(selector, window.document);
    },
    
    doUserFunc: function() {
        jQuery('.nneditor-tag').on('click', function (e) {
            console.log('Click!!!');
        })
    },
    
    appnedChangeContentEditable: function() {
        
        this.getParentObj(this.selectors.tag).on('focus', function() {
            
            nnCore.i('Focus: '+nnCore.getParentObj(this).prop("tagName").toLowerCase()); //
           
            jQuery(this).addClass('nneditor-tag-change');
            nnCore.lastElement = jQuery(this, window.document);
        });
        
        this.getParentObj(this.selectors.tag).dblclick(function() {
            nnCore.i('DblClick: '+nnCore.getParentObj(this).prop("tagName").toLowerCase()); //
            
            jQuery(this).addClass('nneditor-tag-change');
            nnCore.lastElement = jQuery(this, window.document);
        });
        
/*
        this.getParentObj(this.selectors.tag).on('click', function() {
            nnCore.lastElement = jQuery(this);
        });
*/
    },
    
    appendContentEditable: function() {
        
        ALLOW_TAGS.map(function(tag, i) {
            nnCore.getParentObj(tag).attr('contenteditable', true).addClass('nneditor-tag');
        });
        //XXX: KOSTIL FIX ME
        jQuery('.nn-custom').removeAttr('contenteditable');
        jQuery('.nn-custom').removeClass('nneditor-tag');
    },
    
    changeImage: function() {
        jQuery('.nneditor-tag').on('click', function(e) {
            if (jQuery(this).is('img')) {
                
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
        jQuery('body').on('click', '.nn-custom-button', function(e) {
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
                window.document.designMode = "on";
                if (range) {
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
                
                if ($this[0].hasAttribute('data-nn-command')) {
                    command = $this.data('nn-command');
                }
                
                commandParam = null;
                if ($this[0].hasAttribute('data-nn-param')) {
                    commandParam = $this.attr('data-nn-param');
                }
                
                
                window.document.execCommand(command, false, commandParam);
                
                var newNode = window.getSelection().focusNodeNode;
                jQuery(newNode).addClass("nnEditor-user-tag");
                //jQuery(newNode).attr('contenteditable', true);
                
                
                window.document.designMode = "off";
            } else if (
                window.document.selection && window.document.selection.createRange &&
                window.document.selection.type != "None"
            ) {
                // IE case
                range = window.document.selection.createRange();
                range.execCommand(command, false, null);
            }
        }
        
    },
        
    hasUserTag: function() {
        return nnCore.lastElement.hasClass('nnEditor-user-tag');
    },
    
    onSaveContent: function() {
        jQuery('.nn-editor-content').on('click', '#nn_button_save', function () {
            if (confirm("You want to save the file?")) {                 
                nnCore.doClearCoreContent();
                
                var body = jQuery('body').html();
                jQuery.post( "/nbEditor/index.php?url=/save/content/", {
                    'save': 1,
                    'url': jQuery('body').data('nneditor-url'),
                    'body': body,
                    'action': ''
                }, function( data ) {
                     //$this().detach();
                    //window.location.reload();
                });
            }
        });
    },
    
    doClearCoreContent: function() {
        jQuery('#nnIframe').detach();
        
        this.getParentObj('*').removeAttr('contenteditable');
        this.getParentObj('*').removeAttr('data-nneditor');
        
        this.getParentObj('*').removeClass('nneditor-tag');
        this.getParentObj('*').removeClass('nnEditor-user-tag');
    },
    
    onInitColorPicker: function() {
        
        jQuery('.nn-editor-content').on('click', '#colorSelector', function() {
            var $this = jQuery(this);
            jQuery.getScript(nnCore.staticPath+'plugins/colorpicker/js/colorpicker.js', function() {

                 $this.ColorPicker({
                    color: '#0000ff',
                	onShow: function (colpkr) {
                		$(colpkr).fadeIn(500);
                		return false;
                	},
                	onHide: function (colpkr) {
                		$(colpkr).fadeOut(500);
                		return false;
                	},
                	onChange: function (hsb, hex, rgb) {
                		jQuery('#colorSelector div').css('backgroundColor', '#' + hex);
                		jQuery('.js-nn-panel-foreColor').attr('data-nn-param', '#'+hex);
                	}
                });
            });
           
        })
        
    }
}

//nnCore.onInit();
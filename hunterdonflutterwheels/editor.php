<?php
session_start();
$yui_version = '2.5.2';
$ver = '(v0.6)';
$pageaddr = 'yui.editor';
$subject = 'Subdomain Information Editor';
if (file_exists('emailtracker.inc.php')) include('emailtracker.inc.php');
if (!isset($_SESSION['subdom'])) {
	header('location: index.php');
	exit();
}
$subdom = $_SESSION['subdom'];
include('dbconfig.php');
$connect = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect! $dbhost");
$db = mysql_select_db($dbname);
	if (isset($_POST['get'])) {
		$q = "select description from subdomain_information where subdomain_name = '" . mysql_real_escape_string($subdom) . "'";
		$rs = mysql_query($q) or die('Not OK~' . $q . '~' . mysql_error());
		$rw = mysql_fetch_assoc($rs);
		echo "Ok~".$rw['description'];
		exit();
	}
	if (isset($_POST['upd'])) {
		$q = "update subdomain_information set description = '" . mysql_real_escape_string(stripslashes($_POST['upd'])) . "' where subdomain_name = '" . mysql_real_escape_string($subdom) . "'";
	$rs = mysql_query($q) or die('Not OK~' . $q . '~' . mysql_error());
		exit('Ok~Club descriptions updated');
	}
$q = "select club_name, description from subdomain_information where subdomain_name = '" . mysql_real_escape_string($subdom) . "'";
$rs = mysql_query($q);
if ($rs && mysql_num_rows($rs) > 0) {
	$rw = mysql_fetch_assoc($rs);
	$descr = $rw['description'];
	$club_name = $rw['club_name'];
} else {
	$descr = '';
	$club_name = '';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title></title>
<!-- Skin CSS file -->
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/fonts/fonts-min.css">
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/container/assets/skins/sam/container.css">
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/menu/assets/skins/sam/menu.css">
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/button/assets/skins/sam/button.css">
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/assets/skins/sam/skin.css">
<style type="text/css">
    .yui-skin-sam .yui-toolbar-container .yui-toolbar-editcode span.yui-toolbar-icon {
        background-image: url(html_editor.gif);
        background-position: 0 1px;
        left: 5px;
    }
    .yui-skin-sam .yui-toolbar-container .yui-button-editcode-selected span.yui-toolbar-icon {
        background-image: url(html_editor.gif);
        background-position: 0 1px;
        left: 5px;
    }
    .editor-hidden {
        visibility: hidden;
        top: -9999px;
        left: -9999px;
        position: absolute;
    }
    textarea {
        border: 0;
        margin: 0;
        padding: 0;
    }
</style>
<!-- Utility Dependencies -->
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/yahoo-dom-event/yahoo-dom-event.js"></script> 
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/element/element-beta-min.js"></script> 
<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/utilities/utilities.js"></script> 
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/container/container_core-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/menu/menu-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/button/button-min.js"></script>
<!-- Source file for Rich Text Editor-->
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yui_version ?>/build/editor/editor-beta-min.js"></script></head>

<body class="yui-skin-sam">
<form method="post" action="#" id="form1">
<textarea name="editor" id="editor" cols="70" rows="20"><?php echo $descr; ?></textarea>
<button type="button" id="submitEditor">Update Text</button>
<button type="button" id="getEditor">Get Text</button><br>
</form>
<script>

(function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;
    
		var handleSuccess = function(o) {
		    YAHOO.log('Post success', 'info', 'example');
		    var resp = o.responseText.split('~');
		    var data = resp[1];
		    myEditor.setEditorHTML(data);
		}
		var handleFailure = function(o) {
		    YAHOO.log('Post failed', 'info', 'example');
		    var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
		    var data = eval('(' + json + ')');
		    status.innerHTML = 'Status: ' + data.Results.status + '<br>';
		}
		
		var callback = {
		    success: handleSuccess,
		    failure: handleFailure
		};

	var _button = new YAHOO.widget.Button('submitEditor');		
	var _buttonG = new YAHOO.widget.Button('getEditor');		

    var myConfig = {
        height: '300px',
        width: '530px',
        animate: true,
        dompath: true,
        focusAtStart: true,
		  toolbar: {titlebar: '<?php echo $club_name; ?> Information'}

    };

    var state = 'off';
    YAHOO.log('Set state to off..', 'info', 'example');

    YAHOO.log('Create the Editor..', 'info', 'example');
    var myEditor = new YAHOO.widget.Editor('editor', {
	     height: '300px',
        width: '530px',
        animate: true,
        dompath: true,
        focusAtStart: true
		  }
);
    myEditor.on('toolbarLoaded', function() {
        var codeConfig = {
            type: 'push', label: 'Edit HTML Code', value: 'editcode'
        };
        YAHOO.log('Create the (editcode) Button', 'info', 'example');
        var h = this.toolbar._titlebar.firstChild;
        h.innerHTML = '<p style="text-align:center"><?php echo $club_name; ?> Information</p>';

        this.toolbar.addButtonToGroup(codeConfig, 'insertitem');
        
        this.toolbar.on('editcodeClick', function() {
            var ta = this.get('element'),
                iframe = this.get('iframe').get('element');

            if (state == 'on') {
                state = 'off';
                this.toolbar.set('disabled', false);
                YAHOO.log('Show the Editor', 'info', 'example');
                YAHOO.log('Inject the HTML from the textarea into the editor', 'info', 'example');
                this.setEditorHTML(ta.value);
                if (!this.browser.ie) {
                    this._setDesignMode('on');
                }

                Dom.removeClass(iframe, 'editor-hidden');
                Dom.addClass(ta, 'editor-hidden');
                this.show();
                this._focusWindow();
            } else {
                state = 'on';
                YAHOO.log('Show the Code Editor', 'info', 'example');
                this.cleanHTML();
                YAHOO.log('Save the Editors HTML', 'info', 'example');
                Dom.addClass(iframe, 'editor-hidden');
                Dom.removeClass(ta, 'editor-hidden');
                this.toolbar.set('disabled', true);
                this.toolbar.getButtonByValue('editcode').set('disabled', false);
                this.toolbar.selectButton('editcode');
                this.dompath.innerHTML = 'Editing HTML Code';
                this.hide();
            }
            return false;
        }, this, true);

        this.on('cleanHTML', function(ev) {
            YAHOO.log('cleanHTML callback fired..', 'info', 'example');
            this.get('element').value = ev.html;
        }, this, true);
        
        this.on('afterRender', function() {
            var wrapper = this.get('editor_wrapper');
            wrapper.appendChild(this.get('element'));
            this.setStyle('width', '100%');
            this.setStyle('height', '100%');
            this.setStyle('visibility', '');
            this.setStyle('top', '');
            this.setStyle('left', '');
            this.setStyle('position', '');

            this.addClass('editor-hidden');
        }, this, true);
    }, myEditor, true);
    myEditor.render();
	 
    _button.on('click', function(ev) {
        YAHOO.log('Button clicked, initiate transaction', 'info', 'example');
        Event.stopEvent(ev);
        myEditor.saveHTML();
        window.setTimeout(function() {
            var sUrl = "<?php echo $_SERVER['PHP_SELF'] ?>";
            var data = 'upd=' + encodeURIComponent(myEditor.get('textarea').value);
            var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, data);
        }, 200);
    });
    _buttonG.on('click', function(ev) {
        YAHOO.log('Button clicked, initiate transaction', 'info', 'example');
        Event.stopEvent(ev);
        window.setTimeout(function() {
            var sUrl = "<?php echo $_SERVER['PHP_SELF'] ?>";
            var data = 'get=1';
            var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, data);
        }, 200);
    });
})();
</script>
</body>
</html>

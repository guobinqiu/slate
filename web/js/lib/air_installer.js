if (window.__startAirInstall == undefined)
{
    /**
     * put air install swf
     *
     * @param {String} targetId
     * @param {String} swfURL
     * @param {String} airVersion
     * @param {String} appId
     * @param {String} appName
     * @param {String} appVersion
     * @param {String} appUrl
     * @param {String} pubId
     */
    window.__startAirInstall = function (
        targetId, swfURL,
        airVersion, appId, appName, appVersion, appUrl, pubId) {
        //	set id
        var _baseId = 'swf_' + appId;
        var _uniqueId = _baseId + Math.floor(Math.random() * 0x7fffffff).toString(16);

        //	set width and height
        var _width = 380;
        var _height = 145;

        //	create swf object
        var _so = new SWFObject(
            swfURL, _uniqueId, _width, _height, '9.0.115', '#ffffff'
        );

        //	set params
        _so.addParam('allowScriptAccess', 'always');
        _so.addParam('allowNetworking', 'all');
        _so.addParam('wmode', 'transparent');
        //	set variables
        _so.addVariable('airversion', airVersion);
        _so.addVariable('appid', appId);
        _so.addVariable('appname', appName);
        _so.addVariable('appversion', appVersion);
        _so.addVariable('appurl', appUrl);
        _so.addVariable('pubid', pubId);

        //	set referer
        _so.addVariable('referer', window.document.referrer);

        //	put swf content
        var _element;
        try {
            _element = window.document.getElementById(targetId);
        }
        catch (e) { _element = null; }
            if (_element){
                _so.write(targetId);
            } else {
                window.alert(targetId + ' is not exist.');
            }
        };
    }

/*  画像ロールオーバー
------------------------------------------------------------------------
    Standards Compliant Rollover Script
----------------------------------------------------------------------*/
function initRollovers() {
    if ( !document.getElementsByTagName) return

    var aPreLoad = new Array();
    var sTempSrc;
    var aImages = document.getElementsByTagName('img');

    for ( var i = 0; i < aImages.length; i++ ) {
        if ( aImages[i].className == 'imgNav' ) {
            var src = aImages[i].getAttribute('src');
            var ftype = src.substring(src.lastIndexOf('.'), src.length);
            var hsrc = src.replace(ftype, '_on'+ftype);

            aImages[i].setAttribute('hsrc', hsrc);

            aPreLoad[i] = new Image();
            aPreLoad[i].src = hsrc;

            aImages[i].onmouseover = function() {
                sTempSrc = this.getAttribute('src');
                this.setAttribute('src', this.getAttribute('hsrc'));
            }

            aImages[i].onmouseout = function() {
                if ( !sTempSrc) sTempSrc = this.getAttribute('src').replace('_on'+ftype, ftype);
                this.setAttribute('src', sTempSrc);
            }
        }
    }
}

/*  読み込み  ----------------------------------------------------------
------------------------------------------------------------------------*/
onload = function() {
    initRollovers();
}



/*
Written by Guessous Mehdi at http://www.mehdiplugins.com/misc/frontbox.htm
Inspired by all the "lightbox-like" script
original lightbox http://www.huddletogether.com/projects/lightbox/
Licence http://www.gnu.org/copyleft/gpl.html GNU/GPL

checked with jslint:
http://www.jslint.com/

Interesting online javascript compressors :
http://shrinksafe.dojotoolkit.org/
http://dean.edwards.name/packer/
http://yui.2clics.net/
*/

function fbox_engine() {
    if ( typeof(fbox_conf)!= 'function' ) {
        return;
    }//settings missing
    
    var fbox_progress_src,fbox_nbsp_txt;
    var fbox_close_txt,fbox_max_txt, fbox_min_txt;
    var fbox_prev_txt, fbox_next_txt;
    var fbox_disable_fadin;
    //--------------------------
    var loadState = 0;
    
    /* in binary
    bit 1: abort OR termination , add 1
    bit 2: background shown, add 2
    bit 3: progress bar shown, add 4
    bit 4: fbox shown , add 8
    */
    
    var http;
    //---------------------------------------------
    //--- all elements of the doc we always use ---
    var body,fbox_world;
    var fbox_bg,fbox_progress,fbox_progress_img;
    var fbox_fg,fbox_content,fbox_spacer;
    var fbox_bar,fbox_title;
    var fbox_sys,fbox_close_a,fbox_resize_a;
    var fbox_navig,fbox_prev_a,fbox_next_a;
    var fbox_fg_inner;
    var fbox_resize_nbsp;
    //---------------------------------------------
    //---- timer used to prevent flicker ----------
    //---- with adjustOnResizeTime etc .... -----------
    var lastResizeTime=0,lastScrollTime=0,lastScrollPosLeft=0,lastScrollPosTop=0;
    //--------
    var lockHoriz=false; //as soon as popup exceed page size, centering disabled
    var lockVertic=false;
    //----- detect explorer Quirks Mode
    var ieQkMd;
    //---------------------------------------------
    var currFbox=null;
    //---------------------------------------------
    var body_style_begin='';
    //--------------------------------------------
    var blending_bg,blending_fg; //fadin objects
    var posfix_enabled=false;
    //------------------------
    var specialTags=[];
    
    
    //--- "nope" used to disable some links---------------
    function nope() {
        return false;
    }
    
    function _void(t) {
        return;
    }
    
    //----- remove beginning and ending white spaces
    function trim(str) {
        if (!str) {
            return '';
        }
        str=str.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
        return(str);
    }
    
    //-------- event related functions ---------------
    
    function getElem(elemId) {
        return document.getElementById(elemId);
    }
    
    function createElem(elemId) {
        return document.createElement(elemId);
    }
    
    function addEvent(obj, evType, fn) {
        if (obj.addEventListener) {
            obj.addEventListener(evType, fn, false);
            return true;
        }
        if (obj.attachEvent) {
            obj.detachEvent("on"+evType, fn); //try to detach first, this prevents duplicates
            return  obj.attachEvent("on"+evType, fn);
        }
        return false;
    }//end function addEvent
    
    function removeEvent(obj,evType, fn) {
        if (obj.removeEventListener) {
            obj.removeEventListener(evType, fn, false);
            return true;
        }
        if (obj.detachEvent) {
            return obj.detachEvent("on"+evType, fn);
        }
        return false;
    }//end function removeEvent
    
    //----------- ok a function known as domready --------------------------
    //----------  inspired by mootools and some dicussions on web ---------
    //---------- http://mootools.net/
    //----------  http://www.javascriptkit.com/dhtmltutors/domready.shtml
    //---------- http://webreflection.blogspot.com/2007/09/whats-wrong-with-new-iecontentloaded.html
    
    function boostLoad(boostedFunc) {
        var alreadyTest=false;
        
        function alreadyFunc() {
            if (!alreadyTest) {
                alreadyTest=true;
                boostedFunc();
            }
        }//end alreadyFunc
        
        if (window.ActiveXObject) { //internet explorer
            var dummyElem;
            var intervalID= null;
            var f;
            
            dummyElem=document.createElement('div');
            f= function () { //anonymous function f
                var errorScroll;
                errorScroll=false;
                
                try {
                    dummyElem.doScroll('left');
                } catch (e) {
                    errorScroll= true;
                }
                
                if (!errorScroll || alreadyTest ) {
                    if (intervalID) {
                        window.clearInterval(intervalID);
                        intervalID = null;
                    }
                    dummyElem=null; //release mem
                    alreadyFunc();
                }
            };//end anonymous function f
            intervalID=window.setInterval(f,30);
        } //end if internet explorer
        else if (document.addEventListener) {
            document.addEventListener("DOMContentLoaded",alreadyFunc,false);
        }
        
        //------------ fail safe ----------------------
        addEvent(window,"load",alreadyFunc);
    }//end function boostLoad
    
    //--------------------------------------------------------------------------
    function getScrollPos() {
        //body : globally defined
        var doc = document.documentElement;
        this.left = window.pageXOffset || (doc&&doc.scrollLeft) || body.scrollLeft;
        this.top = window.pageYOffset || (doc&&doc.scrollTop) || body.scrollTop;
    }
    
    //--- the code has been a bit complicated, but should provide more reliable results
    //--- "algorithm" elaborated with the help of the comparison table from:
    //--- http://www.softcomplex.com/docs/get_window_size_and_scrollbar_position.html
    
    function getPageSize() {
        //body : globally defined
        var doc = document.documentElement;
        var val1h,val2h,val3h;
        var val1w,val2w,val3w;
        var w=0,h=0;
        var tmp;//for swapping vals
        
        val1h= doc ? doc.clientHeight :0;
        val2h= body.clientHeight ? body.clientHeight :0;
        val3h= window.innerHeight ?  window.innerHeight : 0;
        
        val1w= doc ? doc.clientWidth :0;
        val2w= body.clientWidth ? body.clientWidth :0;
        val3w= window.innerWidth ?  window.innerWidth : 0;
        
        if (val1h && val2h && val3h) { //we have three height settings
            if (val1h == val2h) { //safari
                w=val3w;
                h=val3h;
            } else { //opera or firefox
                if (val2h<val1h) {
                    tmp=val1h;
                    val1h=val2h;
                    val2h=tmp;
                }
                if (val2w<val1w) {
                    tmp=val1w;
                    val1w=val2w;
                    val2w=tmp;
                }
                h= val2h<=val3h ? val2h : val1h;
                w= val2w<=val3w ? val2w : val1w;
            }//end if opera or firefox
        } //end if we have three height settings
        else { //explorer or netscape
            if (val1h) {
                w=val1w;
                h=val1h;
            } else {
                w=val2w;
                h=val2h;
            }
        } //end if explorer or netscape
        
        this.width =w;
        this.height =h;
    }//end of function getPageSize
    
    //--------- setTimeout is a method that really sux ,---
    //-------- when used in a recursive function.----------
    //--------- better use setInterval --------------------
    
    function blendingTransition(elem) {
        var intervalID= null;
        var curr_i=0;
        var i_incr=0; //important
        var i_end=null;
        
        //setting opacity for an element shouldn't be done directly
        //we use halt with  a value instead
        //thus the method  stay "private"
        function setBlend(blend) {
            //if(curr_i==blend) return; //avoid useless work
            if (blend>=100) {
                elem.style.opacity ='';
                elem.style.filter=''; //remove style, avoid bugs in explorer and selects
                elem.style.display='';
            } else if (blend<=0) {
                elem.style.display='none'; //avoid some flicker
                elem.style.opacity = 0; //problem is that we display elements that are supposed to be hidden, in order to calculate size
                elem.style.filter= 'alpha(opacity=0)';
            } else {
                elem.style.opacity = blend*0.01;
                elem.style.filter= 'alpha(opacity=' + blend + ')';
                elem.style.display='';
            }
        }//end function setBlend
        
        function halt(val) {
            var isBusy= intervalID ? true: false;
            if (isBusy) {
                window.clearInterval(intervalID);
                intervalID = null;
            }
            
            i_incr=0; //no begin, no end, avoid bug with redisplay
            val=val-0; //NaN if val undefined
            if (!isNaN(val)) { // warning typeof(NaN) is number !
                curr_i=val; //don't forget to memorize state
                setBlend(val);
            }
            
            return isBusy;
        }  //end function halt()
        
        function redisplay() {
            if (i_incr===0) {
                halt();
            } else {
                if ( (i_incr>0 && curr_i>=i_end) || (i_incr<0 && curr_i<=i_end) ) {
                    halt();
                    curr_i = i_end;
                }
            }
            setBlend(curr_i);
        }//end function redisplay
        
        function majorTask() {
            if ( (i_incr>0 && curr_i<i_end) || (i_incr<0 && curr_i>i_end ) ) {
                setBlend(curr_i);
                curr_i+=i_incr;
                return true;
            }
            
            redisplay();
            return false;
        }//end function majorTask
        
        function animate(j_begin,j_end,j_incr,delay) {
            if (fbox_disable_fadin) {
                halt(j_end);
                return;
            }
            if (!halt()) {
                curr_i = j_begin;
            }
            i_end=j_end;
            i_incr=j_incr;
            //------- let's go -----------------
            //first iteration is immediate
            if (majorTask()) {
                intervalID=window.setInterval(majorTask,delay);
            }
        }//end function animate
        
        //----------------
        //------ making some method publicly available
        this.halt=halt;
        this.animate=animate;
        this.redisplay=redisplay;
    } //end function blendingTransition
    
    //-------------------------------------------------------------------------------
    function setContent(str) {
        if (!str) {
            fbox_content.innerHTML = "";
            fbox_content.style.overflow = "hidden";
            return;
        }
        
        var elem=null;
        if (currFbox.type===0) { //image
            var a=createElem('a');
            a.href="#";
            //-----------------------
            elem=currFbox.objImg;
            elem.id='fbox_content_img';
            elem.style.border='0px';
            //------------------------
            a.appendChild(elem);
            fbox_content.appendChild(a);
        } else {//if NOT  image
            if (currFbox.type==3) { //iframe
                str= '<iframe src="'+currFbox.src+'" id="fbox_content_iframe" frameborder="0" ></iframe>';
            } else {
                if (typeof(str) != 'string') {
                    str='';
                }
            }
            //-------------------------
            fbox_content.innerHTML = str;
            //------------------------
            if (currFbox.type==3) {
                elem = getElem("fbox_content_iframe");
            }
        }//end if NOT  image
        
        fbox_content.style.overflow = "auto";
        
        if (elem) {
            elem.style.width=currFbox.width+'px';
            elem.style.height=currFbox.height+'px';
        }
    }//end function setContent
    
    //--------------------------------------------------------------------------
    function fixSpecialTagsVisibility(fix) {
        var i,j,elems,elem;
        for (j=0;j<specialTags.length;j++) {
            elems=document.getElementsByTagName(specialTags[j]);
            for (i=0; i< elems.length; i++) {
                elem=elems[i];
                if (elem.fbox_doNotFixVisibility) {
                    continue;
                }
                
                if (fix) {
                    elem.fbox_visibilty_backup=elem.style.visibility;
                    elem.style.visibility="hidden";
                } else {
                    elem.style.visibility=elem.fbox_visibilty_backup;
                }
            }//end for i
        } //end for j
    }//end fixSpecialTagVisibility
    
    function adjustBg() {
        var pageSize = new getPageSize();
        var left,top;
        
        if (posfix_enabled) { //position fixed supported
            fbox_bg.style.position="fixed";
            left=top=0;
        }//end if position fixed supported
        else { //position fixed NOT supported
            var scrollPos = new getScrollPos();
            fbox_bg.style.position="absolute";
            left= scrollPos.left;
            top= scrollPos.top;
        }//end if position fixed NOT supported
        
        fbox_bg.style.left= left+'px';
        fbox_bg.style.top= top+'px';
        fbox_bg.style.width = pageSize.width+'px';
        fbox_bg.style.height = pageSize.height+'px';
        blending_bg.redisplay();
    }//end function adjustBg
    
    function showBg() {
        blending_bg.halt(0);
        adjustBg(); //now we can adjust it
        fixSpecialTagsVisibility(true);
        blending_bg.animate(0,77,10,70); //yes!! incr of 10 last state, not a multiple...
        loadState= loadState|2;
    }
    
    function hideBg() {
        loadState= loadState&(~2);
        blending_bg.halt(0);
        fixSpecialTagsVisibility(false);
    }
    
    //--------------------------------------------------------------------------
    //basic centering for progress
    function centerProgress() {
        var pageSize = new getPageSize();
        var left= (pageSize.width - fbox_progress.offsetWidth)>>1;
        var top = (pageSize.height - fbox_progress.offsetHeight)>>1;
        if (posfix_enabled) {
            fbox_progress.style.position="fixed";
        } else {
            var scrollPos = new getScrollPos();
            fbox_progress.style.position="absolute";
            left=scrollPos.left+ left;
            top=scrollPos.top+ top;
        }
        
        fbox_progress.style.left = left+'px';
        fbox_progress.style.top = top+'px';
    } //end function centerProgress
    
    //---- much complicated centering for Fg
    function centerFg() {
        var posfix_fg=false;
        var scrollPos = new getScrollPos();
        var pageSize = new getPageSize();
        fbox_fg.style.display="";
        var left= (pageSize.width - fbox_fg.offsetWidth)>>1;
        var top = (pageSize.height - fbox_fg.offsetHeight)>>1;
        blending_fg.redisplay();
        
        if (posfix_enabled) {
            if (left<=0 || top<=0) {
                posfix_fg=false;
                fbox_fg.style.position="absolute";
            } else {
                posfix_fg=true;
                fbox_fg.style.position="fixed";
            }
        }//end if posfix_enabled
        
        if (left<=0) {//disable centering
            if (!lockHoriz) { //not horizontally locked
                lockHoriz=true;
                left= scrollPos.left+ left;
                if (left<0) {
                    left=0;
                }
                fbox_fg.style.left = left+'px';
            }//end if not horizontally locked
        } else {
            lockHoriz=false;
            if (!posfix_fg) {
                left=scrollPos.left+ left;
            }
            fbox_fg.style.left = left+'px';
        }
        
        if (top<=0) {//disable centering
            if (!lockVertic) {
                lockVertic=true;
                top= scrollPos.top+ top;
                if (top<0) {
                    top=0;
                }
                fbox_fg.style.top = top+'px';
            }
        } else {
            lockVertic=false;
            if (!posfix_fg) {
                top=scrollPos.top+ top;
            }
            fbox_fg.style.top =top+'px';
        }
    }//end function centerFg
    
    
    //----- set  correctly width, height of  fg even when explorer in Quirks mode ---
    //----- also recenter fg --------------------------------------------------------
    //----- forceCaption: -1, hide,
    //------------------ 0, auto
    //------------------ 1, show
    
    function  adjustFg(forceCaption) {
        var fboxWidth = currFbox.width;
        var fboxHeight = currFbox.height;
        var showTitle = trim(currFbox.title) ? true:false;
        var showNavig = currFbox.next || currFbox.prev ? true:false;
        var showCaption = false;
        
        if (!forceCaption) {
            showCaption = showTitle || showNavig;
        } else if (forceCaption == 1) {
            showCaption= true;
        }
        
        if (showCaption && fboxWidth<200) {
            fboxWidth=200; //not less than 200 when caption
        }
        
        if (showCaption) {
            fbox_spacer.style.visibility='visible';
            fbox_spacer.style.display='';
            fbox_bar.style.visibility='visible';
            fbox_bar.style.display='';
            if (showTitle) {
                fbox_title.style.styleFloat=fbox_title.style.cssFloat= 'left';
                fbox_title.style.visibility='visible';
                fbox_title.style.height='auto';
                fbox_title.style.overflow='visible';
                fbox_navig.style.marginTop =3+'px';
            } else {
                fbox_title.style.styleFloat=fbox_title.style.cssFloat= 'right';
                fbox_title.style.visibility='hidden';
                fbox_title.style.height=1+'px';
                fbox_title.style.overflow='hidden';
                fbox_navig.style.marginTop =0+'px';
            }
        }//end if(showCaption)
        else {
            fbox_spacer.style.visibility='hidden'; //silly bug in IE6
            fbox_spacer.style.display='none';
            fbox_bar.style.visibility='hidden';
            fbox_bar.style.display='none';
        } //end else if(showCaption)
        
        if (showNavig) {
            if (currFbox.prev) {
                fbox_prev_a.style.visibility= 'visible';
            } else {
                fbox_prev_a.style.visibility= 'hidden';
            }
            
            if (currFbox.next) {
                fbox_next_a.style.visibility= 'visible';
            } else {
                fbox_next_a.style.visibility= 'hidden';
            }
            
            fbox_navig.style.visibility = 'visible'; //bug or not I do it
            fbox_navig.style.display='';
        } else {
            fbox_navig.style.visibility = 'hidden'; //bug or not I do it
            fbox_navig.style.display='none';
        }
        
        if (ieQkMd) {
            fbox_fg.style.width= (fboxWidth+18) +'px';
            fbox_content.style.width= (fboxWidth+6) +'px';
            fbox_content.style.height= (fboxHeight+6) +'px';
        } else {
            fbox_fg.style.width= (fboxWidth+6) +'px';
            fbox_content.style.width=  fboxWidth +'px';
            fbox_content.style.height= fboxHeight +'px';
        }
        
        if (showCaption) {
            fbox_spacer.style.width= (fboxWidth+6)+'px'; //no padding so stay same
            
            fbox_bar.style.width = (fboxWidth+6) +'px'; //fboxWidth-10 without table/strict
            
            fbox_title.style.width = 'auto';
            fbox_fg.style.height = 'auto';
            fbox_bar.style.height = 'auto';
            
            fbox_fg.style.display = "";
            var w1= fbox_sys.offsetWidth;
            var w2=fbox_title.offsetWidth;
            fbox_fg.style.display = "none";
            w1= fboxWidth-w1-25; //what is remaining when removing everything except "title width" ?
            var w = w1<=190 ? fboxWidth-10 : w1>w2 ? w2:w1;
            fbox_title.style.width = w+ 'px';
            
            fbox_fg.style.display = "";
            var h=fbox_fg.offsetHeight;
            fbox_fg.style.display = "none";
            
            h=h-12; //h-9 without table/strict
            
            fbox_fg.style.height=h+'px';
            fbox_bar.style.height=h-fboxHeight-15+ 'px';
        } //end if(showCaption)
        else { //no caption
            if (ieQkMd) {
                fbox_fg.style.height= (fboxHeight+18) +'px';
            } else {
                fbox_fg.style.height= (fboxHeight+6) +'px';
            }
        } //end if no caption
        
        centerFg();
    }//end function adjustFg
    
    
    //adjustImage ---> adjustFg ---> (display)+ centerFg
    function adjustImage() {
        var objImg= currFbox.objImg;
        var forceCaption=0;
        
        switch (currFbox.imgType) {
        case 0: //auto;
            var pagesize = new getPageSize();
            var x = Math.max(pagesize.width - 125,200); //not less than 200 or it is exasperating
            var y = Math.max(pagesize.height - 125,200);
            var ratio = Math.max(1,Math.max(currFbox.exactImgWidth/x,currFbox.exactImgHeight/y));
            
            if (ratio>1) {
                fbox_resize_a.style.visibility="visible";
                fbox_resize_a.style.display="";
                fbox_resize_nbsp.style.visibility="visible";
                fbox_resize_nbsp.style.display="";
                forceCaption=1;
            } else {
                fbox_resize_a.style.visibility="hidden";
                fbox_resize_a.style.display="none";
                fbox_resize_nbsp.style.visibility="hidden";
                fbox_resize_nbsp.style.display="none";
            }
            
            if (currFbox.imgState===0) {
                fbox_resize_a.innerHTML=fbox_max_txt;
                currFbox.width=objImg.width = Math.round(currFbox.exactImgWidth / ratio);
                currFbox.height=objImg.height = Math.round(currFbox.exactImgHeight/ ratio);
            } else {
                fbox_resize_a.innerHTML= fbox_min_txt;
                currFbox.width=objImg.width = currFbox.exactImgWidth;
                currFbox.height=objImg.height = currFbox.exactImgHeight;
            }
            break;
        case 1: //width is set, keep the ratio
            currFbox.height= objImg.height = Math.round(currFbox.exactImgHeight * currFbox.width/currFbox.exactImgWidth);
            objImg.width = currFbox.width;
            break;
        case 2: //height is set, keep the ratio
            currFbox.width=objImg.width = Math.round(currFbox.exactImgWidth * currFbox.height/currFbox.exactImgHeight);
            objImg.height =currFbox.height;
            break;
        case 3: //width and height set, nothing to compute
            objImg.width = currFbox.width;
            objImg.height = currFbox.height;
        }//end switch (currFbox.imgType)
        
        adjustFg(forceCaption);
    }//end function adjustImage
    
    
    //------------------------------------------------------------------------------
    //--- Remark: the scrolling event can be induced when popup size change --------
    //--- popup exceeds window --> scroll appears ---> background size change --> repostion other elements (wtf ?)
    
    function adjustOnScrollTime() {
        function majorTask() {
            if ((loadState&1) == 1) {
                removeEvent(window,'scroll',adjustOnScrollTime);
                return;
            }
            if ((loadState&8) == 8) {
                centerFg();
            }
            if ((loadState&2) == 2) {
                adjustBg(); //size of background depends of popup, so do that after
            }
            if ((loadState&4) == 4) {
                centerProgress(); //wtf ?
            }
            //----------------------------------
            var now = new Date();
            var currTime = now.getTime();
            lastScrollTime=currTime;
            addEvent(window,'scroll',adjustOnScrollTime);
            //--------------------------------
            var scrollPos = new getScrollPos();
            lastScrollPosLeft=scrollPos.left; //delay adjustOnScrollPos
            lastScrollPosTop=scrollPos.top;
        }//end function majorTask
        
        /* must use removeEvent or doesn't work */
        removeEvent(window,'scroll',adjustOnScrollTime);
        
        var now = new Date();
        var deltaTime = now.getTime()-lastScrollTime;
        if (deltaTime>=200) {
            majorTask();
        } else {
            window.setTimeout(majorTask,200-deltaTime);
        }
    }//end function adjustOnScrollTime
    
    function adjustOnScrollPos() {
        var scrollPos = new getScrollPos();
        if (Math.abs(scrollPos.left-lastScrollPosLeft)>50 || Math.abs(scrollPos.top - lastScrollPosTop>50)) {
            if ((loadState&8) == 8) {
                centerFg();
            }
            if ((loadState&2) == 2) {
                adjustBg(); //size of background depends of popup, so do that after
            }
            if ((loadState&4) == 4) {
                centerProgress(); //wtf ?
            }
            lastScrollPosLeft=scrollPos.left;
            lastScrollPosTop=scrollPos.top;
            //----------------------------
            var now = new Date();
            var currTime = now.getTime();
            lastScrollTime= currTime; //delay also adjustOnScrollTime
        }
    }//end function adjustOnScrollPos
    
    //we excessive flicker by introducing a timer
    //and prevent browser from being too busy
    function adjustOnResizeTime() {
        function majorTask() {
            if ((loadState&1) == 1) {
                removeEvent(window,'resize',adjustOnResizeTime);
                return;
            }
            
            fbox_bg.style.display="none"; //reason of flicker, necessary to compute accurately page size
            fbox_fg.style.display="none";
            
            if ((loadState&8) == 8) {
                if (currFbox.imgType === 0) {
                    setContent(false);
                    adjustImage();
                    setContent(true);
                } else {
                    centerFg();
                }
            }
            if ((loadState&2) == 2) {
                adjustBg(); //size of background depends of popup, so do that after
            }
            if ((loadState&4) == 4) {
                centerProgress(); //wtf ?
            }
            
            //------------------------------
            var now = new Date();
            var currTime = now.getTime();
            lastScrollTime=lastResizeTime=currTime;  //delay also adjustOnScrollTime
            addEvent(window,'resize',adjustOnResizeTime);
            //--------------------------------
            var scrollPos = new getScrollPos();
            lastScrollPosLeft=scrollPos.left; //delay adjustOnScrollPos
            lastScrollPosTop=scrollPos.top;
        }//end function majorTask
        
        /* must use removeEvent or doesn't work */
        removeEvent(window,'resize',adjustOnResizeTime);
        
        var now = new Date();
        var deltaTime = now.getTime()-lastResizeTime;
        if (deltaTime>=200) {
            majorTask();
        } else {
            window.setTimeout(majorTask,200-deltaTime);
        }
    }//end function adjustOnResizeTime
    //--------------------------------------------------------------------------
    function hideProgress() {
        loadState= loadState&(~4);
        fbox_progress.style.display = "none";
        fbox_progress.onclick = null;
    }
    
    function hideFbox() {
        loadState = loadState|1; //termination
        blending_fg.halt(0);
        if (http) {
            http.onreadystatechange = nope; //IE doesn't accept  null
        }
        if (currFbox.objImg) {
            currFbox.objImg.onload=null;
            if (!currFbox.imgReadyTest) { //try to abort image loading
                currFbox.objImg.src=null;
                currFbox.objImg=null;
            }//end if try to abort image loading
        }
        if ((loadState&4)==4) {
            hideProgress();
        }
        loadState= loadState & (~8); //remove fbox
        if ((loadState&2) == 2) {
            hideBg();
        }
        fbox_fg.style.display = "none";
        setContent(false);
        removeEvent(window,'resize',adjustOnResizeTime);
        removeEvent(window,'scroll', adjustOnScrollTime);
        removeEvent(window,'scroll', adjustOnScrollPos);
        //----  restore original body style:
        if (typeof(body.style.cssText) != 'undefined') {
            body.style.cssText=body_style_begin;
        } else {
            body.setAttribute("style",body_style_begin);
        }
        return false;//link is #
    }//end function hideFbox
    
    function showProgress() {
        fbox_progress.style.display = "";
        centerProgress();
        fbox_progress.onclick = hideFbox;
        loadState= loadState|4;
    }
    
//---------------------------------------------------------------------------------------
    function toggleImgState() {
        if ( ((loadState&8) == 8)&& (currFbox.imgType === 0) ) {
            currFbox.imgState = currFbox.imgState ? 0:1;
            setContent(false);
            adjustImage();
            setContent(true);
            if ((loadState&2) == 2) {
                adjustBg(); //size of background depends of popup, so do that after
            }
        }
        return false;
    }//end  function toggleImgState
    
    
//------------------------------------------------------------------------------
    function showFbox(fbObj) {
        function showNext() {
            showFbox(currFbox.next);
            return false; //link is #
        }//end function showNext
        
        function showPrev() {
            showFbox(currFbox.prev);
            return false; //link is #
        }//end function showPrev
        
        if (!fbObj || (typeof(fbObj)!='object')) {
            return;
        }
        //--- works even without this, but according to standard each id -----
        //--- is only for one element (but if not rendered inside DOM  ?) -----
        if (currFbox && (typeof(currFbox)=='object') && currFbox.objImg && (typeof(currFbox.objImg)=='object')) {
            currFbox.objImg.id=null;
        }
        //----- ok, now the current fbox, is the one shown ---------------
        currFbox=fbObj;
        
        var src= currFbox.src;
        
        body.style.width='auto';//don't let external css change default behavior
        body.style.height='auto';
        
        lockHoriz=lockVertic= false; //prevent centering when fbox exceeds page size
        lastScrollPosLeft=0;
        lastScrollPosTop=0;
        fbox_resize_a.style.visibility="hidden";
        fbox_resize_a.style.display="none";
        fbox_resize_nbsp.style.visibility="hidden";
        fbox_resize_nbsp.style.display="none";
        
        setContent(false);
        // set title here
        var tmp_title= trim(currFbox.title);
        if (!tmp_title || tmp_title=='.') {
            tmp_title="&nbsp;";
        }
        fbox_title.innerHTML = tmp_title;
        tmp_title= null;
        
        blending_fg.halt(0);
        loadState= loadState & 2; //remove all except background
        
        if ((loadState&2) === 0)  {
            showBg();
        }
        
        adjustOnResizeTime();
        adjustOnScrollTime();
        if (!posfix_enabled) {
            addEvent(window,'scroll', adjustOnScrollPos);
        }
        
        if (currFbox.type === 0 || currFbox.type == 2) {
            showProgress();
        }
        if (currFbox.type === 0) {
            fbox_content.onclick= hideFbox;
            fbox_resize_a.onclick=toggleImgState;
        } else {
            fbox_content.onclick= null;
            fbox_resize_a.onclick=nope; // facultative, but safer
        }
        
        //--------- set prev and next actions ------------
        fbox_prev_a.onclick = currFbox.prev ? showPrev: nope;
        fbox_next_a.onclick = currFbox.next ? showNext: nope;
        
        //---------- now the "loading part" -------------
        //-----------------------------------------------
        
        switch (currFbox.type) {
        case 0: //picture
        
            var imgReadyFunc = function() {
                var tmpImgReadyTest=currFbox.imgReadyTest;
                currFbox.imgReadyTest=true;
                if ((loadState&1) != 1) {
                    hideProgress();
                    if (!tmpImgReadyTest) {
                        currFbox.exactImgWidth=currFbox.objImg.width;
                        currFbox.exactImgHeight=currFbox.objImg.height;
                    }
                    adjustImage();
                    setContent(true);
                    if (tmpImgReadyTest) {
                        blending_fg.animate(0,100,25,20);
                    } else {
                        blending_fg.animate(0,100,10,35);
                    }
                    loadState=loadState|8;
                }
            };//end function imgReadyFunc
            
            if (currFbox.imgReadyTest) {
                imgReadyFunc();
            } else {
                var imgPreloader = new Image();
                currFbox.objImg=imgPreloader;
                
                imgPreloader.onload = function() {
                    this.onload=null;
                    this.onerror=null;
                    imgReadyFunc();
                };
                
                imgPreloader.onerror = function() {
                    this.onerror=null;
                    hideFbox();
                };
                
                imgPreloader.src = src;
            }
            break;
            
        case 1: //Support for overlay of div on existing page
            //nothing to load
            adjustFg(1);
            var strHTML = currFbox.tagContent.innerHTML;
            setContent(strHTML);
            blending_fg.animate(0,100,10,30);
            loadState=loadState|8;
            break;
            
        case 2: //using ajax to retrieve a file
            try {
                http.open('GET',src,true);
            } catch (e) {
                currFbox.tagLink.onclick=nope;
                hideFbox();
                break;
            }
            
            http.onreadystatechange = function() {
                if ((loadState&1) == 1) {
                    // see http://www.quirksmode.org/blog/archives/2005/09/xmlhttp_notes_a_1.html
                    http.onreadystatechange = nope;
                    http.abort();
                } else if (http.readyState == 4) {
                    http.onreadystatechange = nope; //IE ...
                    if (http.status == 200 || !http.status) {
                        hideProgress();
                        adjustFg(1);
                        var response = http.responseText;
                        setContent(response);
                        blending_fg.animate(0,100,10,30);
                        loadState=loadState|8;
                    } else {
                        currFbox.tagLink.onclick= nope;
                        hideFbox();
                    }
                }//end if (http.readyState == 4)
            };
            
            http.send(null);
            break;
        case 3://iframe
            adjustFg(1);
            setContent(true);
            blending_fg.animate(0,100,10,30);
            loadState=loadState|8;
        }//end switch
    }//end function showFbox
    
//------------------------------------------------
    function init_fbox() {
        if (getElem("fbox_world")) {
            return;
        } //note: fbox_engine should be called only once
        
        //how to make/use http request properly, see there:
        //  http://blogs.msdn.com/ie/archive/2006/01/23/516393.aspx
        //  http://keelypavan.blogspot.com/2006/03/reusing-xmlhttprequest-object-in-ie.html
        //remark: window.XMLHttpRequest works only if target file truly online with IE7beta3
        
        http = null;
        try {
            if (window.XMLHttpRequest) { // If IE7, Mozilla, Safari, etc: Use native object
                http=new XMLHttpRequest();
            } else if (window.ActiveXObject) {// ...otherwise, use the ActiveX control for IE5.x and IE6
                http=new ActiveXObject("Microsoft.XMLHTTP");
            }
        } catch (e) {}
        
        var cst= new fbox_conf();
        fbox_disable_fadin=cst.disable_fadin;
        fbox_progress_src = cst.progress_src;
        fbox_close_txt = cst.close_txt;
        fbox_prev_txt = cst.prev_txt;
        fbox_next_txt = cst.next_txt;
        fbox_max_txt = cst.max_txt;
        fbox_min_txt = cst.min_txt;
        
        fbox_nbsp_txt='';
        for (var i=0; i<cst.nbsp_count; i++) {
            fbox_nbsp_txt +='&nbsp;';
        }
        
        //------------------------------------------------------------------
        var strHTML = '<div id="fbox_bg" style="display:none;"></div>' +
                      '<div id="fbox_progress" style="display:none;"></div>' + //will insert progress image after
                      '<div id="fbox_fg" style="display:none;">' +
                      '<div id="fbox_fg_inner">'+
                      '<div id="fbox_content"></div>' +
                      '<div id="fbox_spacer"></div>' +
                      //------------- our caption --------------------------------
                      '<table id="fbox_bar" border="0" cellspacing="0" cellpadding="0"><tr><td>' + //why a table? IE5 with small windows
                      '<div id="fbox_sys">' +
                      '<nobr>'+ //for IE5
                      '<a id="fbox_resize_a" href="#" >'+ fbox_max_txt + '</a>' +
                      '<span id="fbox_resize_nbsp" >' + fbox_nbsp_txt +  '</span>' + //IE5 doesn't supports margins for inline elements
                      '<a id="fbox_close_a" href="#" >' + fbox_close_txt + '</a>' +
                      '</nobr>'+
                      '</div>'+ //close for "fbox_sys"
                      '<div id="fbox_title">&nbsp;</div>' +
                      '<div id="fbox_navig">' +
                      '<nobr>'+ //for IE5
                      '<a id="fbox_prev_a" href="#" >' + fbox_prev_txt + '</a>' +
                      fbox_nbsp_txt +
                      '<a id="fbox_next_a" href="#" >' + fbox_next_txt + '</a>' +
                      '</nobr>'+
                      '</div>' + //close for "fbox_navig"
                      '</td></tr></table>' + //close for "fbox_bar"
                      //------------ end of caption --------------------------------
                      '</div>' + //close for "fbox_fg_inner"
                      '</div>' ; //close for "fbox_fg"
                      
        fbox_world = document.createElement("div");
        fbox_world.setAttribute("id","fbox_world");
        fbox_world.innerHTML = strHTML;
        
        body = document.getElementsByTagName("body")[0];
        body.appendChild(fbox_world);
        //-----------------
        //save original body style
        body_style_begin= typeof(body.style.cssText) != 'undefined' ? body.style.cssText : body.getAttribute("style");
        
        //--- all elements of the fbox we always use --------------
        fbox_bg=getElem("fbox_bg"); fbox_progress=getElem("fbox_progress");
        fbox_fg=getElem("fbox_fg"); fbox_content=getElem("fbox_content"); fbox_spacer=getElem("fbox_spacer");
        fbox_bar=getElem("fbox_bar"); fbox_title=getElem("fbox_title");
        fbox_sys=getElem("fbox_sys"); fbox_close_a=getElem("fbox_close_a"); fbox_resize_a=getElem("fbox_resize_a");
        fbox_navig=getElem("fbox_navig"); fbox_prev_a=getElem("fbox_prev_a"); fbox_next_a=getElem("fbox_next_a");
        fbox_fg_inner=getElem("fbox_fg_inner"); //this one is just for white background...
        fbox_resize_nbsp=getElem("fbox_resize_nbsp");
        
        //------------- close link, always same action:
        fbox_close_a.onclick = hideFbox;
        
        //----- let's define two "blending transitions" ---------------
        //blendingTransition(elem)
        blending_fg = new blendingTransition(fbox_fg);
        blending_bg = new blendingTransition(fbox_bg);
        
        //----- let's detect explorer Quirks Mode -------------------------------
        //---- what a silly test, but It works even with a fake "user agent" ----
        fbox_content.style.visibility='hidden';
        fbox_fg.style.width = 100+'px';
        fbox_content.style.width='auto';
        fbox_fg.style.left=-200+'px'; //let's put this outside preview
        fbox_fg.style.top=-200+'px';
        fbox_fg.style.display = ''; //must display to get properties
        ieQkMd = fbox_content.offsetWidth<100; //silly test, we get exactely 100 with other modes
        fbox_fg.style.display = 'none';
        fbox_content.style.visibility='visible';
        fbox_fg.style.left=0+'px'; //facultative, I think
        fbox_fg.style.top=0+'px';
        
        //-------- is position fixed supported ? -------------------
        //--- posfix doesn't work in ie7 if "quirck mode"  ---------
        posfix_enabled=!ieQkMd;
        //by default yes, except old internet explorer versions
        if (window.ActiveXObject && !window.XMLHttpRequest) {
            posfix_enabled=false;
        }
        
        //----------- let's preload the progress image -------------
        fbox_progress_img = new Image();
        var tmpAppendedTest=false;
        fbox_progress_img.onload= function() {
            this.onload=null;
            this.onerror=null;
            this.style.width= this.width +'px';
            this.style.height= this.height +'px';
            this.style.border='0px';
            if (!tmpAppendedTest) {
                tmpAppendedTest=true;
                fbox_progress.appendChild(this);
            }
        };
        
        fbox_progress_img.onerror = function () {
            this.onerror=null;
            //never mind -------------------
            if (!tmpAppendedTest) {
                tmpAppendedTest=true;
                fbox_progress.appendChild(this);
            }
        };
        
        fbox_progress_img.src=fbox_progress_src;
        specialTags=['select','object','embed'];
        var fbox_stack= []; //array literal notation
        
        //------------------------------------------------------------------
        //------- let's define a class that will store some params ---------
        //------- and allow to launch fbox ---------------------------------
        function fbox_kind(tagFB) {
            var _this=this;
            this.tagLink=tagFB.parentNode;
            this.title = tagFB.getAttribute('title');
            var src= trim(tagFB.getAttribute('src'));
            if (!src) {
                src= trim(this.tagLink.getAttribute('href'));
            }
            this.src=src;
            
            this.type = 3; //by default iframe
            var typeStr = trim(tagFB.getAttribute('type')).toLowerCase();
            switch (typeStr) {
            case 'image':
                this.type = 0;
                break;
            case 'inline':
                this.type = 1;
                break;
            case 'ajax':
                this.type = 2;
                break;
            case 'iframe':
                this.type = 3;
                break;
            default:
                if (src.indexOf("#")>=0) {
                    this.type = 1;
                    break;
                }
                
                var dot = src.lastIndexOf(".");
                if (dot<1) {
                    break;
                }
                var ext = src.substr(dot+1,src.length).toLowerCase(); //add 1 to remove the dot
                if ( ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' ) {
                    this.type = 0;
                }
            }//end switch(typeStr)
            
            this.tagContent= null;
            if (this.type==1) {
                var elemSrcId = src.substr(src.indexOf("#")+1,1000);
                this.tagContent= getElem(elemSrcId);
                
                if (this.tagContent) {//let's mark all select elements of the hidden div
                    var i,j,elems;
                    for (j=0;j<specialTags.length;j++) {
                        elems=this.tagContent.getElementsByTagName(specialTags[j]);
                        for (i=0; i< elems.length; i++) {
                            elems[i].fbox_doNotFixVisibility=true;
                        }//end for i
                    }//end for j
                } //end if let's mark all select elements of the hidden div
                else {
                    this.type=-1;
                }
            } //end if(this.type==1)
            
            if (this.type==2 && !http) {
                this.type=-1;
            }
            
            this.objImg=null;
            this.imgType=-1;
            this.exactImgWidth=0;
            this.exactImgHeight=0;
            this.imgState=-1;       /* -1 undefined
                                 0 minimized
                                 1 maximized
                             */
            this.imgReadyTest=false; //if image is loaded , don't reload it
            this.width=  tagFB.getAttribute('width')-0; // substract 0 so that string converted in number
            this.height = tagFB.getAttribute('height')-0; // substract 0 so that string converted in number
            
            if (this.type===0) { //this is an image
                this.imgType=0;
                if (this.width && this.width>0 )  {
                    this.imgType+=1;
                } else {
                    this.width = 200;
                }
                
                if (this.height && this.height>0 ) {
                    this.imgType+=2;
                } else {
                    this.height = 150;
                }
                
                this.imgState = this.imgType ? 1:0;
                
                //---- special: images have a title attribute ------------------
                //---- so if there's no title attribute in frontbox tag --------
                //---- let's use the one from the thumbnail (if there's one) ---
                
                if (!trim(this.title)) { //no title defined in frontbox tag
                    var imgs=this.tagLink.getElementsByTagName('img');
                    if (imgs[0]) { //should be at least one image
                        var newtitle= imgs[0].getAttribute('title');
                        if (trim(newtitle)) {
                            this.title= newtitle;
                        }
                    }//end if should be at least one image
                }//end if no title defined in frontbox tag
                
            }//end if this is an image
            else {
                if (!this.width) {
                    this.width=400;
                }
                if (!this.height) {
                    this.height=300;
                }
            }
            
            //---------------------------------------------------------
            //----------- set next and previous link ------------------
            //--- must declare public, otherwise cannot create list
            
            this.next=null;
            this.prev=null;
            var name=trim(tagFB.getAttribute('name')).toLowerCase();
            if (name && this.type != -1 ) { //ok we have a set
                if (fbox_stack[name]) { //already a previous element
                    var n= fbox_stack[name].length;
                    this.prev= fbox_stack[name][n-1];
                    this.prev.next= this;
                    fbox_stack[name][n]= this; //add the new element
                } //end if already a previous element
                else { //inserting first elem of set
                    fbox_stack[name]=[]; //array literal notation
                    fbox_stack[name][0]= this;
                } //end inserting first elem of set
            }//end if ok we have a set
            
            //---------------------------------------------------------
            function ready() {
                showFbox(_this);
                return false; //otherwise, would follow link
            }
            
            this.tagLink.onclick = this.type == -1 ? nope : ready;
            //---- for debug --------
            //if(this.type != -1 ) this.tagLink.href="javascript:void(0);";
        }//end function fbox_kind
        
        //----------------------------------------------------------
        //check <frontbox > tags
        var spans = document.getElementsByTagName("span");
        
        //var i: already defined in scope
        for ( i=0; i< spans.length; i++) {
            var span = spans[i];
            if ( span.className=="frontbox" && span.parentNode.nodeName.toLowerCase() == "a" ) { // check if parent is a link
                _void(new fbox_kind(span)); // just to make jslint happy
            }
        } //end for i
    }//end function init_fbox()
    
    //addEvent(window, 'load',init_fbox); //let's do-it right now
    //---- onReady like func ----------------------
    boostLoad(init_fbox);
}//end of function fbox_engine

fbox_engine(); //and go !
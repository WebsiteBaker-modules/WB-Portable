/**
 * 
 */
domReady(function() {

//    LoadOnFly ( 'body', WB_URL + '/modules/droplets/js/draggabilly.pkgd.js' );
//    LoadOnFly ( 'body', WB_URL + '/modules/droplets/js/modal.js' );

    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }

    function addEvent(elem, event, fn) {
        if (elem.addEventListener) {
            elem.addEventListener(event, fn, false);
        } else {
            elem.attachEvent("on" + event, function() {
                // set the this pointer same as addEventListener when fn is called
                return(fn.call(elem, window.event));   
            });
        }
    }
/**
 * 
 */
 
 
    function mouseX (e) {
      if (e.pageX) {
        return e.pageX;
      }
      if (e.clientX) {
        return e.clientX + (document.documentElement.scrollLeft ?
                            document.documentElement.scrollLeft :
                            document.body.scrollLeft);
      }
      return null;
    }

    function mouseY (e) {
      if (e.pageY) {
        return e.pageY;
      }
      if (e.clientY) {
        return e.clientY + (document.documentElement.scrollTop ?
                            document.documentElement.scrollTop :
                            document.body.scrollTop);
      }
      return null;
    }

    function dragable (clickEl,dragEl) {
      var p = get(clickEl);
      var t = get(dragEl);
      var drag = false;
      offsetX = 0;
      offsetY = 0;
      var mousemoveTemp = null;
    
      if (t) {
        var move = function (x,y) {
          t.style.left = (parseInt(t.style.left)+x) + "px";
          t.style.top  = (parseInt(t.style.top) +y) + "px";
        }
        var mouseMoveHandler = function (e) {
          e = e || window.event;
    
          if(!drag){return true};
    
          var x = mouseX(e);
          var y = mouseY(e);
          if (x != offsetX || y != offsetY) {
            move(x-offsetX,y-offsetY);
            offsetX = x;
            offsetY = y;
          }
          return false;
        }
        var start_drag = function (e) {
          e = e || window.event;
    
          offsetX=mouseX(e);
          offsetY=mouseY(e);
          drag=true; // basically we're using this to detect dragging
    
          // save any previous mousemove event handler:
          if (document.body.onmousemove) {
            mousemoveTemp = document.body.onmousemove;
          }
          document.body.onmousemove = mouseMoveHandler;
          return false;
        }
        var stop_drag = function () {
          drag=false;      
    
          // restore previous mousemove event handler if necessary:
          if (mousemoveTemp) {
            document.body.onmousemove = mousemoveTemp;
            mousemoveTemp = null;
          }
          return false;
        }
        p.onmousedown = start_drag;
        p.onmouseup = stop_drag;
      }
    }


    function move(ev) {
      ev.dataTransfer.setData('text', ev.target.id);
    }

    window.addEventListener("load",function () {
      initCheckboxes();
      var dragItems = document.querySelectorAll("[draggable=true]")
console.info( dragItems );
      for (var i = 0; i < dragItems.length; i++) {
        var draggable = dragItems[i];
        draggable.addEventListener("dragstart",move);
      };
    });

//    addEvent( window, 'load', initCheckboxes );

    function initCheckboxes() {
        addEvent(document.getElementById('select_all'), 'click', setCheckboxes);
    }
    function setCheckboxes() {
        var cb = document.getElementById( 'cb-droplets' ).getElementsByTagName('input');
console.info(cb);
        var isChecked = document.getElementById('select_all').checked;
        for (var i = 0; i < cb.length; i++) {
            cb[i].checked = isChecked;
        }
    }


    function selectSingleElement(IdSuffix, el ) {
        document.getElementById(el.id + IdSuffix).checked ='checked'; 
        document.getElementById('select_all').checked =false;
    }

    function deselectAllElements(IdSuffix, el ) {
        for ( i = 0;; i++) {
            if (!(e = document.getElementById('L' + i + IdSuffix))) {
                break;
            }
            e.checked = el.checked;
        }
    }




/**
 * 
    function OnSubmitForm( elm ) {
      if( elm.value ) {
console.info( elm.value );
//         document.droplets.action = delm.value;
      }
      return true;
    }

    function OnSubmit( elm ) {
console.info( elm.value );
//         document.droplets.action = document.pressed;
    }
 */


});
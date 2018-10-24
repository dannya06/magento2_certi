/*
	Author	: I.CUBE, inc.
    JS for Responsive
*/

define([
	"jquery"
], function($, ui){
	"use strict";
	
	// Init breakpoint pixel values (keep consistent with css).
    function Responsive() {

	    var bp = {
		    screen__xxs: 320,
		    screen__xs: 480,
		    screen__s: 640,
		    screen__m: 768,
		    screen__l: 1024,
		    screen__xl: 1440
		}
	    
	    this.screen__xxs = bp.screen__xxs;
	    this.screen__xs = bp.screen__xs;
	    this.screen__s = bp.screen__s;
	    this.screen__m = bp.screen__m;
	    this.screen__l = bp.screen__l;
	    this.screen__xl = bp.screen__xl;
	
	    // Core breakpoints (keep consistent with css).
	    this.screen__xxs = 'handheld, screen and (max-width: ' + (this.screen__xxs) + 'px)';
	    this.screen__xs = 'handheld, screen and (max-width: ' + this.screen__xs + 'px)';
	    this.screen__s = 'handheld, screen and (max-width: ' + this.screen__s + 'px)';
	    this.screen__m = 'handheld, screen and (max-width: ' + (this.screen__m -1) + 'px)';
	    this.screen__l = 'handheld, screen and (max-width: ' + this.screen__l + 'px)';
	    this.screen__xl = 'handheld, screen and (max-width: ' + this.screen__xl + 'px)';
    }

	var _responsive = new Responsive();

    return _responsive;
	
});
   
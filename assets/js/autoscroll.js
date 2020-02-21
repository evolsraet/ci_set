"use strict";

/*
	Autoscroll Library

	Kang Minho, 2019-06-18

	사용법
		- var autoscroll = Autoscroll(); 설정 후
		- autoscroll.method() 실행

*/

function Autoscroll(e) {
	// alert( typeof e );
	if( typeof e === 'undefined' )
		this.element = $(document);
	else
		this.element = e;
	
	this.base_speed = 200;
	this.slowest_speed = 500;
	this.fastest_speed = 10;

	this.speed = this.base_speed;
	this.loop = null;
	this.is_scroll = false;

	this.get_speed = function() {
		return Math.abs(this.speed - this.slowest_speed) / 100;
		// return this.speed;
	}

	this.adjust = function(speed) {
		if( speed == 'slower' )
			speed = 30;
		else
			speed = -30;

		this.speed = this.speed + speed;

		if( this.speed > this.slowest_speed )
			this.speed = this.slowest_speed;
		else if( this.speed < this.fastest_speed )
			this.speed = this.fastest_speed;

		return this.get_speed();
	}

	this.stop = function() {
		this.is_scroll = false;
		clearTimeout( this.loop );
	}

	this.play = function() {
		var final_position = $(this.element).height() - $(window).height() ;
		// console.log( final_position );
		// console.log( this.element + ' - scrollTop : ' + $(this.element).scrollTop() );
		// console.log( this.element + ' - height : ' + $(this.element).height() );

		if( final_position == $(this.element).height() ) {
			this.stop();
		} else {
			this.is_scroll = true;

			$(this.element).scrollTop( $(this.element).scrollTop() + 1);
			// window.scrollBy(0, this.speed);
			this.loop = setTimeout(
							this.play.bind(this),
							this.speed
						);
		}
	}

	this.toggle = function() {
		if( this.is_scroll == false )
			this.play();
		else
			this.stop();

		return this.is_scroll;
	}
}

// Autoscroll.prototype.get_speed = function() {
// 	return this.speed;
// };

// Autoscroll.prototype.adjust_speed = function(speed) {
// 	return this.speed = this.speed + speed;
// };

// Autoscroll.prototype.play = function() {
// 	console.log( this.speed );
// 	window.scrollBy(0, this.speed);
// 	this.loop = setTimeout(this.play, 10);
// };

// Autoscroll.prototype.stop = function() {
// 	clearTimeout( this.loop );
// };

var $j = jQuery.noConflict();

$j( document ).on( 'ready', function() {
	// Popup
	oceanwpPopupLogin();
} );

/* ==============================================
POPUP LOGIN
============================================== */
function oceanwpPopupLogin() {
	"use strict"

	// Return if logged in
	if ( oceanwpLocalize.loggedIn ) {
		return;
	}

	// Show/hive form
	var oplShowHideDivs = function( ids_array ) {
		if ( ids_array.constructor === Array ) {
			var length = ids_array.length;

			for ( var i = 0; i < length; i++ ) {
				if ( ids_array[ i ].constructor === Array && 2 === ids_array[ i ].length ) {
					var jqElement = $j( ids_array[ i ][ 0 ] );
					if ( jqElement.length ) {
						if ( 1 === ids_array[ i ][ 1 ] ) {
							jqElement.removeClass( 'opl-hide' ).addClass( 'opl-show' );
						} else {
							jqElement.removeClass( 'opl-show' ).addClass( 'opl-hide' );
						}
					}
				}
			}
		}
	};

	// Vars
	var loginForm = $j( '#opl-login-form' );

	// Add href attr if custom link
	$j( '.opl-link-wrap a' ).attr( {
		'href': '#opl-login-form',
	} );

	// Open the popup
	$j( document ).on( 'click', '.opl-link, .opl-link-wrap a, .sidr-class-opl-link', function( e ) {
		e.preventDefault();

		var innerWidth = $j( 'html' ).innerWidth();
		$j( 'html' ).css( 'overflow', 'hidden' );
		var hiddenInnerWidth = $j( 'html' ).innerWidth();
		$j( 'html' ).css( 'margin-right', hiddenInnerWidth - innerWidth );

		// Show form
		loginForm.fadeIn();

		// Add effect class
		loginForm.addClass( 'is-visible' );

		// Show the login form
		oplShowHideDivs( [
			[ '.opl-login', 1 ],
			[ '.opl-register', 0 ],
			[ '.opl-reset-password', 0 ]
		] );

		// Focus
		$j( '#opl_user_login' ).focus();

		// Hide message
		$j( '.opl-errors' ).hide();

		// Clear fields
		$j( '.input-lg' ).val( '' );

	} );

	// Close the popup
	$j( '.opl-close-button, .opl-overlay' ).on( 'click', function( e ) {

		if ($j("meta[name='wpim-popup-condition']").length) {
			$j("meta[name='wpim-popup-condition']").attr("content", 'not-from-form');
		}

		e.preventDefault();

		setTimeout( function() {
			$j( 'html' ).css( {
				'overflow': '',
				'margin-right': '' 
			} );
		}, 300);

		// Hide form
		loginForm.fadeOut();

		// Remove effect class
		loginForm.removeClass( 'is-visible' );
	} );

	// Login
	$j( '.login-link' ).on( 'click', function( e ) {
		e.preventDefault();

		// Show the login form
		oplShowHideDivs( [
			[ '.opl-login', 1 ],
			[ '.opl-register', 0 ],
			[ '.opl-reset-password', 0 ]
		] );

		setTimeout( function() {
			$j( '#opl_user_login' ).focus();
		}, 100 );
	} );

	// Register
	$j( '.register-link' ).on( 'click', function( e ) {
		e.preventDefault();

		// Show the register form
		oplShowHideDivs( [
			[ '.opl-login', 0 ],
			[ '.opl-register', 1 ],
			[ '.opl-reset-password', 0 ]
		] );

		setTimeout( function() {
			$j( '#opl_register_login' ).focus();
		}, 100 );
	} );

	// Forgot pass
	$j( '.forgot-pass-link' ).on( 'click', function( e ) {
		e.preventDefault();

		// Show the reset password form
		oplShowHideDivs( [
			[ '.opl-login', 0 ],
			[ '.opl-register', 0 ],
			[ '.opl-reset-password', 1 ]
		] );

		setTimeout( function() {
			$j( '#opl_user_or_email' ).focus();
		}, 100 );
	} );

	// Login form
	$j( '#opl_login_form' ).on( 'submit', function( e ) {
		e.preventDefault();

		var button 		= $j( this ).find( 'button' ),
			redirect 	= $j( this ).find( 'input[name="redirect_to"]' );

		button.button( 'loading' );

		$j.post( oceanwpLocalize.ajaxURL, $j( this ).serialize(), function( data ) {

			var obj = $j.parseJSON( data ),
				err = $j( '.opl-login .opl-errors' );

			err.show();
			err.html( obj.message );

			if ( obj.error == false ) {
				$j( '#opl-login-form .opl-login-wrap' ).addClass( 'loading' );

				if ($j("meta[name='wpim-popup-condition']").length && $j("meta[name='wpim-popup-condition']").attr('content') === 'from-form') {
					if ( $j("#wpim-form").length ) {
						$j("#wpim-form").submit();
					}
				} else {
					if ( redirect.length ) {
						window.location.href = redirect.val();
					} else {
						window.location.reload( true );
					}
				}
				
			}

			button.button( 'reset' );
		} );

	} );

	// Register form
	$j( '#opl_registration_form' ).on( 'submit', function( e ) {
		e.preventDefault();

		var button 		= $j( this ).find( 'button' ),
			redirect 	= $j( this ).find( 'input[name="redirect_to"]' );

		button.button( 'loading' );

		$j.post( oceanwpLocalize.ajaxURL, $j( this ).serialize(), function( data ) {

			var obj = $j.parseJSON( data ),
				err = $j( '.opl-register .opl-errors' );

			err.show();
			err.html( obj.message );

			if ( obj.error == false ) {
				$j( '#opl-login-form .opl-login-wrap' ).addClass( 'registration-complete' );
				
				if ($j("meta[name='wpim-popup-condition']").length && $j("meta[name='wpim-popup-condition']").attr('content') === 'from-form') {
					if ( $j("#wpim-form").length ) {
						$j("#wpim-form").submit();
					}
				} else {
					if ( redirect.length ) {
						window.location.href = redirect.val();
					} else {
						window.location.reload( true );
					}
				}
				
			}

			button.button( 'reset' );

		} );

	} );

	// Rreset Password form
	$j( '#opl_reset_password_form' ).on( 'submit', function( e ) {
		e.preventDefault();

		var button = $j( this ).find( 'button' );

		button.button( 'loading' );

		$j.post( oceanwpLocalize.ajaxURL, $j( this ).serialize(), function( data ) {

			var obj = $j.parseJSON( data ),
				err = $j( '.opl-reset-password .opl-errors' );

			err.show();
			err.html( obj.message );

			button.button( 'reset' );

		} );

	} );

}

/* ========================================================================
 * Bootstrap: button.js v3.3.7
 * http://getbootstrap.com/javascript/#buttons
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */
+
function( $ ) {
	'use strict';

	// BUTTON PUBLIC CLASS DEFINITION
	// ==============================

	var Button = function( element, options ) {
		this.$element = $( element )
		this.options = $.extend( {}, Button.DEFAULTS, options )
		this.isLoading = false
	}

	Button.VERSION = '3.3.7'

	Button.DEFAULTS = {
		loadingText: 'loading...'
	}

	Button.prototype.setState = function( state ) {
		var d = 'disabled'
		var $el = this.$element
		var val = $el.is( 'input' ) ? 'val' : 'html'
		var data = $el.data()

		state += 'Text'

		if ( data.resetText == null ) $el.data( 'resetText', $el[ val ]() )

		// push to event loop to allow forms to submit
		setTimeout( $.proxy( function() {
			$el[ val ]( data[ state ] == null ? this.options[ state ] : data[ state ] )

			if ( state == 'loadingText' ) {
				this.isLoading = true
				$el.addClass( d ).attr( d, d ).prop( d, true )
			} else if ( this.isLoading ) {
				this.isLoading = false
				$el.removeClass( d ).removeAttr( d ).prop( d, false )
			}
		}, this ), 0 )
	}

	Button.prototype.toggle = function() {
		var changed = true
		var $parent = this.$element.closest( '[data-toggle="buttons"]' )

		if ( $parent.length ) {
			var $input = this.$element.find( 'input' )
			if ( $input.prop( 'type' ) == 'radio' ) {
				if ( $input.prop( 'checked' ) ) changed = false
				$parent.find( '.active' ).removeClass( 'active' )
				this.$element.addClass( 'active' )
			} else if ( $input.prop( 'type' ) == 'checkbox' ) {
				if ( ( $input.prop( 'checked' ) ) !== this.$element.hasClass( 'active' ) ) changed = false
				this.$element.toggleClass( 'active' )
			}
			$input.prop( 'checked', this.$element.hasClass( 'active' ) )
			if ( changed ) $input.trigger( 'change' )
		} else {
			this.$element.attr( 'aria-pressed', !this.$element.hasClass( 'active' ) )
			this.$element.toggleClass( 'active' )
		}
	}


	// BUTTON PLUGIN DEFINITION
	// ========================

	function Plugin( option ) {
		return this.each( function() {
			var $this = $( this )
			var data = $this.data( 'bs.button' )
			var options = typeof option == 'object' && option

			if ( !data ) $this.data( 'bs.button', ( data = new Button( this, options ) ) )

			if ( option == 'toggle' ) data.toggle()
			else if ( option ) data.setState( option )
		} )
	}

	var old = $.fn.button

	$.fn.button = Plugin
	$.fn.button.Constructor = Button


	// BUTTON NO CONFLICT
	// ==================

	$.fn.button.noConflict = function() {
		$.fn.button = old
		return this
	}


	// BUTTON DATA-API
	// ===============

	$( document )
		.on( 'click.bs.button.data-api', '[data-toggle^="button"]', function( e ) {
			var $btn = $( e.target ).closest( '.btn' )
			Plugin.call( $btn, 'toggle' )
			if ( !( $( e.target ).is( 'input[type="radio"], input[type="checkbox"]' ) ) ) {
				// Prevent double click on radios, and the double selections (so cancellation) on checkboxes
				e.preventDefault()
				// The target component still receive the focus
				if ( $btn.is( 'input,button' ) ) $btn.trigger( 'focus' )
				else $btn.find( 'input:visible,button:visible' ).first().trigger( 'focus' )
			}
		} )
		.on( 'focus.bs.button.data-api blur.bs.button.data-api', '[data-toggle^="button"]', function( e ) {
			$( e.target ).closest( '.btn' ).toggleClass( 'focus', /^focus(in)?$/.test( e.type ) )
		} )

}( jQuery );
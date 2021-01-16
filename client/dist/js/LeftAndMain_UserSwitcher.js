/*jslint browser: true, nomen: true*/
/*global $, window, jQuery*/
(function($) {
	'use strict';
	$.entwine('ss', function($) {

		$('#UserSwitcherSelect').entwine({
			onadd:function(){
				this.on('change', function(){
					// window.location.search=$.query.set('MemberID', $(this).val());
					window.location.href = 'admin/cmsuserswitcher_xhr?UserSwitcherMemberID=' + $(this).val() + '&BackURL=' + window.location.href;
				});
			}
		});

		/*
		 * Reload subsites dropdown when links are processed
		 */
		$('.cms-container .cms-menu-list li a').entwine({
			onclick: function(e) {
				$('.cms-container').loadFragment('admin/cmsuserswitcher_xhr', 'CMSUserSwitcherMemberList');
				this._super(e);
			}
		});

	});
}(jQuery));

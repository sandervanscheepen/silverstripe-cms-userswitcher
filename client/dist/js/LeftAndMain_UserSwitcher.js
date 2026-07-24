/*jslint browser: true, nomen: true*/
/*global $, window, jQuery*/
(function ($) {
  'use strict';
  $.entwine('ss', function ($) {

    $('#UserSwitcherSelect').entwine({
      onadd: function () {
        this.on('change', function () {
          // window.location.search=$.query.set('MemberID', $(this).val());
          window.location.href = cmsUserSwitcherGetAdminRootURLSegment() + '/cmsuserswitcher_xhr?UserSwitcherMemberID=' + $(this).val() + '&BackURL=' + window.location.href;
        });
      }
    });

    /*
     * Reload subsites dropdown when links are processed.
     */
    $('.cms-container .cms-menu-list li a').entwine({
      onclick: function (e) {
        $('.cms-container').loadFragment(cmsUserSwitcherGetAdminRootURLSegment() + '/cmsuserswitcher_xhr', 'CMSUserSwitcherMemberList');
        this._super(e);
      }
    });
  });
}(jQuery));

function cmsUserSwitcherGetAdminRootURLSegment()
{
  if(typeof window.ssAdminRootURL !== "undefined" ) {
    return window.ssAdminRootURL;
  }

  // Silverstripe 6: window.ssAdminRootURL was removed. Build an ABSOLUTE admin
  // root from the <base> href + ss.config.adminUrl, otherwise assigning a
  // relative URL to window.location resolves against the current admin section
  // (e.g. /admin/pages) and produces a broken /admin/admin/... path.
  var adminUrl = (window.ss && window.ss.config && window.ss.config.adminUrl) ? window.ss.config.adminUrl : 'admin';
  var baseEl = document.querySelector('base');
  var base = baseEl ? baseEl.href.replace(/\/$/, '') : window.location.origin;
  return base + '/' + adminUrl.replace(/^\//, '');
}

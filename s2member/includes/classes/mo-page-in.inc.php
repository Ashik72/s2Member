<?php
/**
 * Membership Options Page (inner processing routines).
 *
 * Copyright: © 2009-2011
 * {@link http://www.websharks-inc.com/ WebSharks, Inc.}
 * (coded in the USA)
 *
 * Released under the terms of the GNU General Public License.
 * You should have received a copy of the GNU General Public License,
 * along with this software. In the main directory, see: /licensing/
 * If not, see: {@link http://www.gnu.org/licenses/}.
 *
 * @package s2Member\Membership_Options_Page
 * @since 3.5
 */
if(realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_mo_page_in"))
	{
		/**
		 * Membership Options Page (inner processing routines).
		 *
		 * @package s2Member\Membership_Options_Page
		 * @since 3.5
		 */
		class c_ws_plugin__s2member_mo_page_in
		{
			/**
			 * Forces a redirection to the Membership Options Page for s2Member.
			 *
			 * This can be used by 3rd party apps that are not aware of which Page is currently set as the Membership Options Page.
			 * Example usage: `http://example.com/?s2member_membership_options_page=1`
			 *
			 * Redirection URLs containing array brackets MUST be URL encoded to get through: ``wp_sanitize_redirect()``.
			 *   So we pass everything to ``urlencode_deep()``, as an array. It handles this via ``_http_build_query()``.
			 *   See bug report here: {@link http://core.trac.wordpress.org/ticket/17052}
			 *
			 * @package s2Member\Membership_Options_Page
			 * @since 3.5
			 *
			 * @attaches-to ``add_action("init");``
			 *
			 * @return null Or exits script execution after redirection w/ `301` status.
			 */
			public static function membership_options_page /* Real Membership Options Page. */
			()
				{
					do_action("ws_plugin__s2member_before_membership_options_page", get_defined_vars());

					if(!empty ($_GET["s2member_membership_options_page"]) && is_array($_g = c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_GET))))
						{
							$args = /* Initialize this to an empty array value. */
								array();

							foreach($_g as $var => $value) // Include all of the `_?s2member_` variables.
								// Do NOT include `s2member_membership_options_page`; creates a redirection loop.
								if(preg_match("/^_?s2member_/", $var) && $var !== "s2member_membership_options_page")
									$args[$var] = /* Supports nested arrays. */
										$value;

							wp_redirect(add_query_arg(urlencode_deep($args), get_page_link($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"])), 301).exit ();
						}

					do_action("ws_plugin__s2member_after_membership_options_page", get_defined_vars());
				}

			/**
			 * Redirects to Membership Options Page w/ MOP Vars.
			 *
			 * Redirection URLs containing array brackets MUST be URL encoded to get through: ``wp_sanitize_redirect()``.
			 *   So we pass everything to ``urlencode_deep()``, as an array. It handles this via ``_http_build_query()``.
			 *   See bug report here: {@link http://core.trac.wordpress.org/ticket/17052}
			 *
			 * @package s2Member\Membership_Options_Page
			 * @since 111101
			 *
			 * @param str     $seeking_type Seeking content type. One of: `post|page|catg|ptag|file|ruri`.
			 * @param str|int $seeking_type_value Seeking content type data. String, or a Post/Page ID.
			 * @param str     $req_type Access requirement type. One of these values: `level|ccap|sp`.
			 * @param str|int $req_type_value Access requirement. String, or a Post/Page ID.
			 * @param str     $seeking_uri The full URI that access was attempted on.
			 * @param str     $res_type Restriction type that's preventing access.
			 *   One of: `post|page|catg|ptag|file|ruri|ccap|sp|sys`.
			 *   Defaults to ``$seeking_type``.
			 *
			 * @return bool This function always returns true.
			 *
			 * @TODO Update documentation in the API Scripting section.
			 */
			public static function wp_redirect_w_mop_vars($seeking_type = FALSE, $seeking_type_value = FALSE, $req_type = FALSE, $req_type_value = FALSE, $seeking_uri = FALSE, $res_type = FALSE)
				{
					do_action("ws_plugin__s2member_before_wp_redirect_w_mop_vars", get_defined_vars());

					foreach(array("seeking_type", "seeking_type_value", "req_type", "req_type_value", "seeking_uri", "res_type") as $_param)
						{
							if($_param === "seeking_uri" || ($_param === "seeking_type_value" && $seeking_type === "ruri"))
								${$_param} = base64_encode((string)${$_param});
							else ${$_param} = str_replace(array(",", ";"), "", (string)${$_param});
						}
					unset($_param); // Housekeeping.

					if(!$res_type) $res_type = $seeking_type;

					$vars = $res_type.",".$req_type.",".$req_type_value.";";
					$vars .= $seeking_type.",".$seeking_type_value.",".$seeking_uri;
					$vars = array("_s2member_vars" => $vars);

					$status = apply_filters("ws_plugin__s2member_content_redirect_status", 301, get_defined_vars());
					$status = apply_filters("ws_plugin__s2member_wp_redirect_w_mop_vars_status", $status, get_defined_vars());

					$mop_url = get_page_link($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"]);
					$mop_url = add_query_arg(urlencode_deep($vars), $mop_url);
					$mop_url = c_ws_plugin__s2member_utils_urls::add_s2member_sig($mop_url);

					wp_redirect($mop_url, $status); // NOTE: we do not exit here (on purpose).

					do_action("ws_plugin__s2member_after_wp_redirect_w_mop_vars", get_defined_vars());

					return TRUE; // Always returns true here.
				}
		}
	}
?>
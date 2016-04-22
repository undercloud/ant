<?php
namespace Ant\Plugins;

use Ant\Exception;
use Ant\Ant;
/**
 * Google Analytics service
 */
class GoogleAnalytics extends Base
{
	/**
	 * Embed script
	 *
	 * @param string $code   api key
	 * @param string $domain domain
	 *
	 * @return string
	 */
	public function embed($code, $domain = '')
	{
		if (empty($code)) {
			throw new Exception('Code must contained a valid Google Analytics UA code.');
		}

		if (!empty($domain)) {
			$domain = "_gaq.push(['_setDomainName', '".$domain."']);";
		}

		return "
		<script>
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '{$code}']);
			{$domain}
			_gaq.push(['_setAllowLinker', true]);
			_gaq.push(['_trackPageview']);
			(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript';
			  ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' :
			  'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0];
			  s.parentNode.insertBefore(ga, s);
			})();
		</script>";
	}

	/**
	 * Register plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function register(Ant $ant)
	{
		$ant->register('ga', function ($code, $domain = '') {
			$ga = new self();

			return $ga->embed($code, $domain);
		});
	}

	/**
	 * Unregister plugin
	 *
	 * @param Ant\Ant $ant instance
	 *
	 * @return void
	 */
	public function unregister(Ant $ant)
	{
		$ant->unregister('ga');
	}
}
?>
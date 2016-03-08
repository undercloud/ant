<?php
	namespace Ant\Plugins;

	class GoogleAnalytics extends Base
	{
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

		public function register($ant)
		{
			$ant->register('ga', function($code, $domain = '') {
				$ga = new self();

				retrn $ga->embed($code, $domain);
			});
		}
	}
?>
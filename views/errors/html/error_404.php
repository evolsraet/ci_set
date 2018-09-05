<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
		<title>This page could not be found</title>

		<style>
			.nuxt-progress {
				position: fixed;
				top: 0px;
				left: 0px;
				right: 0px;
				height: 2px;
				width: 0%;
				-webkit-transition: width 0.2s, opacity 0.4s;
				transition: width 0.2s, opacity 0.4s;
				opacity: 1;
				background-color: #efc14e;
				z-index: 999999;
			}
			html {
				font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
				font-size: 16px;
				word-spacing: 1px;
				-ms-text-size-adjust: 100%;
				-webkit-text-size-adjust: 100%;
				-moz-osx-font-smoothing: grayscale;
				-webkit-font-smoothing: antialiased;
				-webkit-box-sizing: border-box;
								box-sizing: border-box;
			}
			*, *:before, *:after {
				-webkit-box-sizing: border-box;
								box-sizing: border-box;
				margin: 0;
			}
			.button--green {
				display: inline-block;
				border-radius: 4px;
				border: 1px solid #3b8070;
				color: #3b8070;
				text-decoration: none;
				padding: 10px 30px;
			}
			.button--green:hover {
				color: #fff;
				background-color: #3b8070;
			}
			.button--grey {
				display: inline-block;
				border-radius: 4px;
				border: 1px solid #35495e;
				color: #35495e;
				text-decoration: none;
				padding: 10px 30px;
				margin-left: 15px;
			}
			.button--grey:hover {
				color: #fff;
				background-color: #35495e;
			}
			.__nuxt-error-page {
				padding: 16px;
				padding: 1rem;
				background: #F7F8FB;
				color: #47494E;
				text-align: center;
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-webkit-box-pack: center;
						-ms-flex-pack: center;
								justify-content: center;
				-webkit-box-align: center;
						-ms-flex-align: center;
								align-items: center;
				-webkit-box-orient: vertical;
				-webkit-box-direction: normal;
						-ms-flex-direction: column;
								flex-direction: column;
				font-family: sans-serif;
				font-weight: 100 !important;
				-ms-text-size-adjust: 100%;
				-webkit-text-size-adjust: 100%;
				-webkit-font-smoothing: antialiased;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
			}
			.__nuxt-error-page .error {
				max-width: 450px;
			}
			.__nuxt-error-page .title {
				font-size: 24px;
				font-size: 1.5rem;
				margin-top: 15px;
				color: #47494E;
				margin-bottom: 8px;
			}
			.__nuxt-error-page .description {
				color: #7F828B;
				line-height: 21px;
				margin-bottom: 10px;
			}
			.__nuxt-error-page a {
				color: #7F828B !important;
				text-decoration: none;
			}
			.__nuxt-error-page .logo {
				position: fixed;
				left: 12px;
				bottom: 12px;
			}
		</style>
	</head>
	<body>
		<div id="__nuxt">
			<div id="__layout">
				<div>
					<div class="__nuxt-error-page">
						<div class="error">
							<svg xmlns="http://www.w3.org/2000/svg"
								width="90"
								height="90"
								fill="#DBE1EC"
								viewBox="0 0 48 48">
								<path d="M22 30h4v4h-4zm0-16h4v12h-4zm1.99-10C12.94 4 4 12.95 4 24s8.94 20 19.99 20S44 35.05 44 24 35.04 4 23.99 4zM24 40c-8.84 0-16-7.16-16-16S15.16 8 24 8s16 7.16 16 16-7.16 16-16 16z"></path>
							</svg>
							<div class="title">
								This page could not be found
							</div>
							<p class="description">
								<?=$message?>
								<br><br>
								<a href="/" class="error-link nuxt-link-active">Back to the home page</a>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>

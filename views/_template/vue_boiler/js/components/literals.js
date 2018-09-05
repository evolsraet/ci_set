const literals = Vue.component('literals',{
	template: `
		<div>
			<h5>literals</h5>
			<pre>{{ data }}</pre>
			<input type="text" class="form-control" v-model="data" />
		</div>
	`,
	data: function () {
		return {
			data: '-- loading --'
		}
	},
	mounted: function () {
		this.get_ajax();
	},
	methods: {
		get_ajax: function(){
			comp = this;
			axios
				.get('https://api.coindesk.com/v1/bpi/currentprice.json')
				.then(function (response) {
					comp.data = response.data.disclaimer ;
				});
		}
	}
});
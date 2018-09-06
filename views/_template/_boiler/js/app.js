// Vue.js
// Vue.http.headers.common['X-CSRF-TOKEN'] = $("#token").attr("value");

Vue.http.headers.common['csrf_token'] = document.head.querySelector("[name=csrf_token]").content;

var vm = new Vue({
	el: '#app',
	data: {
		message: '메세지1'
	},
	mounted() {
		this.get_msg();
		// axios.get("/home/test/a")
		//   		.then(response => {
		//   			console.log( response );
		//   			this.message = response.data
		//   		})
		//   		.catch(error => {
		//   			console.log(error);
		//   		});
  	},
  	methods: {
  		get_msg: function() {
			this.$http.get('/home/test/a')
				.then(response => {
					// success callback
					console.log(response);
					this.message = response.data;
				}, response => {
					// error callback
					console.log(response);
			});
  		}
  	}
});
	// HttpVueLoader.LangProcessor.Less = function (lessText) {
	// 	return new Promise (function (resolve, reject) {
	// 		Less.Render (lesssText, {}, function (err, CSS) {
	// 			if (err) reject (err);
	// 			Resolve (CSS);
	// 		});
	// 	})
	// }

    // httpVueLoader.langProcessor.less = function (lessText) {
    //     return new Promise(function(resolve, reject) {
    //         less.compile(lessText, function (result) {
    //             if ( result.status === 0 )
    //                 resolve(result.text)
    //             else
    //                 reject(result)
    //         });
    //     });
    // }

	Vue.component('button-counter', {
		template: '<button v-on:click="incrementCounter">{{ counter }}</button>',
		data: function () {
			return {
			  counter: 0
			};
		},
		methods: {
			incrementCounter: function () {
			  this.counter += 1;
			  this.$emit('total_up');
			}
		},
	});


	// 앱
	var app = new Vue({
		el: '#vue_test',
		components: {
			'todo-item': httpVueLoader('/views/pages/vue/todo-item.vue')
		},
		data: {
			title: '문단 제목',
			message: 'NOTHING',
			seen: true,
			groceryList: [
				{ id: 0, text: 'Vegetables' },
				{ id: 1, text: 'Cheese' },
				{ id: 2, text: 'Whatever else humans are supposed to eat' }
			],
			total: 0,
		},
		watch: {
			title: function (newTitle) {
				this.message = '입력 대기중...';
				this.getTitle();
			},
		},
		methods: {
			toggle_seen: function () {
				this.seen = !this.seen;
			},
			getTitle: _.debounce(
				function(){
					this.message = "생각중...";
					this.message = 'COMPLETE : ' + this.title;
				}, 500
			),
			// getTitle: function(){
			// 	this.message = 'COMPLETE : ' + this.title;
			// },
		    incrementTotal: function () {
		    	this.total += 1;
		    },
		}
	});
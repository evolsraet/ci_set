<script type="text/x-template" id="todo-item">
	<li :id="todo.id">{{ todo.text }}</li>
</script>

<script>
	Vue.component('todo-item', {
		props: ['todo'],
		template: '#todo-item'
	});
</script>
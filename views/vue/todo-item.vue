<template>
	<li class="todo_item" :id="todo.id">{{ todo.text }}</li>
</template>

<script>
    module.exports = {
        props: ['todo'],
        data: function(){
        	return {
        	};
        }
    };
</script>

<style scoped>
	li.todo_item {
		color: red;
	}
</style>
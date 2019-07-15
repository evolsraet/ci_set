<!-- 스크립트 로드 -->
<? // $this->assets->load_js( "https://cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js" ); // 개발 ?>
<? $this->assets->load_js( "https://unpkg.com/vue" ); // 개발 ?>
<? $this->assets->load_js( "https://unpkg.com/http-vue-loader" ); // 개발 ?>

<? // $this->assets->load_js( 'https://unpkg.com/axios@0.12.0/dist/axios.min.js', false ); ?>
<? // $this->assets->load_js( 'https://unpkg.com/lodash@4.13.1/lodash.min.js', false ); ?>
<? // $this->assets->load_js( 'https://cdn.jsdelivr.net/npm/vue', false ); // 프로덕션 ?>

<div id="vue_test">
	<div class="well">
		<button
			type="button"
			class="btn btn-primary"
			v-on:click="toggle_seen"
			>
			seen 토글
		</button>
	</div>
	<span v-bind:title="message">
		[{{ title }}]
	</span>
	<p v-if="seen">{{ message }}</p>
	<ol>
		<!-- todo-item 컴포넌트의 인스턴스 만들기 -->
		<li
			is="todo-item"
			v-for="item in groceryList"
			v-bind:todo="item"
			v-bind:key="item.id"
			>
		</li>
	</ol>
	<input
		class="form-control"
		v-model="title">

	<template v-if="seen">
		<h1>seen</h1>
	</template>
	<template v-else>
		<h1>!seen</h1>
	</template>

	<div id="counter-event-example">
		<p>{{ total }}</p>
		<button-counter v-on:total_up="incrementTotal"></button-counter>
		<button-counter v-on:total_up="incrementTotal"></button-counter>
	</div>


</div>

<? // =vue_component(MODULEPATH."vue/todo_item.vue")?>
<? $this->assets->load_js( VIEWDIR.'pages/vue/vue_test.js' ); // 개발 ?>
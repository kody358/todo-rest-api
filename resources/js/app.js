import './bootstrap';
import { createApp } from 'vue';
import TodoApp from './components/TodoApp.vue';

// Vueアプリケーションを作成
const app = createApp({});

// TodoAppコンポーネントをケバブケースで登録
app.component('todo-app', TodoApp);

// #appエレメントにマウント
app.mount('#app');

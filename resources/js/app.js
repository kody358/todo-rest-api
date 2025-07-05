import './bootstrap';
import { createApp } from 'vue';
import TodoApp from './components/TodoApp.vue';
import LoginForm from './components/LoginForm.vue';
import AppMain from './components/AppMain.vue';

// Vueアプリケーションを作成
const app = createApp(AppMain);

// TodoAppコンポーネントをケバブケースで登録
app.component('todo-app', TodoApp);
app.component('login-form', LoginForm);

// #appエレメントにマウント
app.mount('#app');

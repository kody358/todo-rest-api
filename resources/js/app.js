import './bootstrap';
import { createApp } from 'vue';
import TodoApp from './components/TodoApp.vue';
import LoginForm from './components/LoginForm.vue';
import RegisterForm from './components/RegisterForm.vue';
import AuthView from './components/AuthView.vue';
import AppMain from './components/AppMain.vue';

// Vueアプリケーションを作成
const app = createApp(AppMain);

app.component('todo-app', TodoApp);
app.component('login-form', LoginForm);
app.component('register-form', RegisterForm);
app.component('auth-view', AuthView);

// #appエレメントにマウント
app.mount('#app');

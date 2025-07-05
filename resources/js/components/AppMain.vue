<template>
  <div>
    <!-- ログイン状態に応じて表示を切り替え -->
    <LoginForm v-if="!isAuthenticated" @login-success="handleLoginSuccess" />
    <TodoApp v-else />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuth } from '../composables/useAuth'
import LoginForm from './LoginForm.vue'
import TodoApp from './TodoApp.vue'

const { isAuthenticated, login } = useAuth()

const handleLoginSuccess = (authData) => {
  login(authData)
}
</script> 
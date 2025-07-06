<template>
  <div>
    <!-- ログイン状態に応じて表示を切り替え -->
    <AuthView v-if="!isAuthenticated" @auth-success="handleAuthSuccess" />
    <TodoApp v-else />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuth } from '../composables/useAuth'
import AuthView from './AuthView.vue'
import TodoApp from './TodoApp.vue'

const { isAuthenticated, login } = useAuth()

const handleAuthSuccess = (authData) => {
  login(authData)
}
</script> 
<template>
  <div>
    <!-- ログインフォーム -->
    <LoginForm 
      v-if="currentView === 'login'"
      @login-success="handleAuthSuccess"
      @switch-to-register="currentView = 'register'"
    />
    
    <!-- 登録フォーム -->
    <RegisterForm 
      v-else-if="currentView === 'register'"
      @register-success="handleAuthSuccess"
      @switch-to-login="currentView = 'login'"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import LoginForm from './LoginForm.vue'
import RegisterForm from './RegisterForm.vue'

const emit = defineEmits(['auth-success'])

// デフォルトはログイン画面
const currentView = ref('login')

const handleAuthSuccess = (authData) => {
  emit('auth-success', authData)
}
</script> 
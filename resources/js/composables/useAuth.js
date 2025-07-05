import { ref, computed } from 'vue'

const token = ref(localStorage.getItem('auth_token'))
const user = ref(null)

// 初期化時にユーザー情報を取得
if (token.value) {
  try {
    const userData = localStorage.getItem('user')
    if (userData) {
      user.value = JSON.parse(userData)
    }
  } catch (error) {
    console.error('ユーザー情報の読み込みに失敗しました:', error)
  }
}

export const useAuth = () => {
  const isAuthenticated = computed(() => !!token.value)
  
  const login = (authData) => {
    token.value = authData.token
    user.value = authData.user
    localStorage.setItem('auth_token', authData.token)
    localStorage.setItem('user', JSON.stringify(authData.user))
  }
  
  const logout = async () => {
    try {
      await fetch('/api/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      })
    } catch (error) {
      console.error('ログアウトリクエストに失敗しました:', error)
    }
    
    // ローカルストレージからトークンとユーザー情報を削除
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
  }
  
  const getAuthHeader = () => {
    return token.value ? { 'Authorization': `Bearer ${token.value}` } : {}
  }
  
  return {
    token,
    user,
    isAuthenticated,
    login,
    logout,
    getAuthHeader
  }
} 
<template>
  <div class="container mx-auto p-4">
    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Todo管理</h1>
      <div class="flex items-center space-x-4">
        <span class="text-gray-600">こんにちは、{{ user?.name }}さん</span>
        <button
          @click="handleLogout"
          class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors"
        >
          ログアウト
        </button>
      </div>
    </div>

    <!-- 作成フォーム -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <h2 class="text-xl font-bold mb-4">新しいTodoを作成</h2>
      <form @submit.prevent="createTodo" class="space-y-4">
        <div>
          <input
            v-model="form.title"
            type="text"
            placeholder="タイトル（100文字まで）"
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div>
          <textarea
            v-model="form.content"
            placeholder="内容（省略可 1000文字まで）"
            rows="3"
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <button
          type="submit"
          class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          作成
        </button>
      </form>
    </div>

    <!-- フィルタ -->
    <div class="mb-6">
      <select
        v-model="statusFilter"
        class="p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="all">すべて</option>
        <option value="pending">未完了</option>
        <option value="completed">完了</option>
      </select>
    </div>

    <!-- エラーメッセージ（インライン表示） -->
    <div v-if="message.text && message.type === 'error'" class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg mb-6">
      {{ message.text }}
    </div>
    
    <!-- 空の状態 -->
    <div v-if="todos.length === 0" class="text-center py-8">
      <p class="text-gray-600">
        {{ hasFilters ? '条件に一致するTodoが見つかりません' : 'Todoが登録されていません' }}
      </p>
    </div>
    
    <!-- Todoリスト -->
    <div v-else class="space-y-4">
      <div
        v-for="todo in todos"
        :key="todo.id"
        class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 
              :class="{ 'line-through text-gray-500': todo.completed }"
              class="text-lg font-semibold mb-2"
            >
              {{ todo.title }}
            </h3>
            <p 
              v-if="todo.content"
              :class="{ 'text-gray-400': todo.completed }"
              class="text-gray-600 mb-3"
            >
              {{ todo.content }}
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-2 ml-4">
            <button
              @click="toggleComplete(todo)"
              :class="todo.completed ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600'"
              class="px-4 py-2 text-white rounded-lg transition-colors"
            >
              {{ todo.completed ? '未完了' : '完了' }}
            </button>
            <button
              @click="deleteTodo(todo.id)"
              class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
            >
              削除
            </button>
            <button
              v-if="todo.deleted_at"
              @click="restoreTodo(todo.id)"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
            >
              復元
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 成功メッセージ用モーダル -->
    <div v-if="showSuccessModal" class="fixed top-8 left-1/2 transform -translate-x-1/2 z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4 border border-gray-200">
        <div class="flex items-center justify-center mb-4">
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
        </div>
        <p class="text-center text-gray-700 mb-6">{{ successMessage }}</p>
        <button 
          @click="closeSuccessModal"
          class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors"
        >
          閉じる
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useAuth } from '../composables/useAuth'

const { user, getAuthHeader, logout } = useAuth()

// リアクティブな状態管理
const todos = ref([])
const isLoading = ref(false)
const statusFilter = ref('all')
const showSuccessModal = ref(false)
const successMessage = ref('')

// フォームデータ
const form = reactive({
  title: '',
  content: ''
})

// メッセージ管理
const message = reactive({
  text: '',
  type: 'success' // 'success' | 'error'
})

// 計算プロパティ
const hasFilters = computed(() => {
  return statusFilter.value !== 'all'
})

// ウォッチャー（フィルタ変更時に再取得）
watch(statusFilter, () => {
  fetchTodos()
})

// ライフサイクルフック
onMounted(() => {
  fetchTodos()
})

// CSRFトークンを取得
const getCsrfToken = () => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
}

// APIリクエストのデフォルトオプション
const getRequestOptions = (method = 'GET', data = null) => {
  const options = {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...getAuthHeader()
    }
  }
  
  const token = getCsrfToken()
  if (token) {
    options.headers['X-CSRF-TOKEN'] = token
  }
  
  if (data) {
    options.body = JSON.stringify(data)
  }
  
  return options
}

// Todoリストの取得
const fetchTodos = async () => {
  isLoading.value = true
  
  let url = '/api/todos'
  const params = new URLSearchParams()
  
  if (statusFilter.value !== 'all') {
    params.append('status', statusFilter.value)
  }
  
  if (params.toString()) {
    url += '?' + params.toString()
  }
  
  const response = await fetch(url, getRequestOptions())
  const result = await response.json()
  
  if (response.ok) {
    todos.value = result.data.data || []
  } else {
    if (response.status === 401) {
      handleUnauthorized()
    } else {
      showError(result.message || 'データの取得に失敗しました')
    }
  }
  
  isLoading.value = false
}

// Todo作成
const createTodo = async () => {
  if (!form.title.trim()) {
    showError('タイトルを入力してください')
    return
  }
  
  const response = await fetch('/api/todos', getRequestOptions('POST', form))
  const result = await response.json()
  
  if (response.ok) {
    showSuccess('Todoが作成されました')
    resetForm()
    fetchTodos()
  } else {
    if (response.status === 401) {
      handleUnauthorized()
    } else {
      showError(result.message || 'Todoの作成に失敗しました')
    }
  }
}

// 完了状態の切り替え
const toggleComplete = async (todo) => {
  const response = await fetch(`/api/todos/${todo.id}`, getRequestOptions('PUT', {
    title: todo.title,
    content: todo.content,
    completed: !todo.completed
  }))
  
  const result = await response.json()
  
  if (response.ok) {
    showSuccess(`Todoを${!todo.completed ? '完了' : '未完了'}に変更しました`)
    fetchTodos()
  } else {
    if (response.status === 401) {
      handleUnauthorized()
    } else {
      showError(result.message || 'Todoの更新に失敗しました')
    }
  }
}

// Todo削除
const deleteTodo = async (id) => {
  if (!confirm('このTodoを削除しますか？')) {
    return
  }
  
  const response = await fetch(`/api/todos/${id}`, getRequestOptions('DELETE'))
  const result = await response.json()
  
  if (response.ok) {
    showSuccess('Todoが削除されました')
    fetchTodos()
  } else {
    if (response.status === 401) {
      handleUnauthorized()
    } else {
      showError(result.message || 'Todoの削除に失敗しました')
    }
  }
}

// Todo復元
const restoreTodo = async (id) => {
  const response = await fetch(`/api/todos/${id}/restore`, getRequestOptions('PUT'))
  const result = await response.json()
  
  if (response.ok) {
    showSuccess('Todoが復元されました')
    fetchTodos()
  } else {
    if (response.status === 401) {
      handleUnauthorized()
    } else {
      showError(result.message || 'Todoの復元に失敗しました')
    }
  }
}

// ログアウト処理
const handleLogout = async () => {
  await logout()
  window.location.reload()
}

// 認証エラー処理
const handleUnauthorized = () => {
  showError('認証が必要です。再度ログインしてください。')
  setTimeout(() => {
    logout()
    window.location.reload()
  }, 2000)
}

// フォームリセット
const resetForm = () => {
  form.title = ''
  form.content = ''
}

// 成功メッセージの表示（モーダル）
const showSuccess = (text) => {
  successMessage.value = text
  showSuccessModal.value = true
  // 1秒後に自動で閉じる
  setTimeout(() => {
    closeSuccessModal()
  }, 1000)
}

// 成功モーダルを閉じる
const closeSuccessModal = () => {
  showSuccessModal.value = false
  successMessage.value = ''
}

// エラーメッセージの表示（インライン）
const showError = (text) => {
  message.text = text
  message.type = 'error'
  setTimeout(() => {
    message.text = ''
  }, 5000)
}

</script>

import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/employee',
    name: 'employee',
    component: () => import('../views/Employee.vue')
  },
  {
    path: '/show_employee1',
    name: 'show_employee',
    component: () => import('../views/Show_employee.vue')
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router

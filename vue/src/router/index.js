import { createRouter, createWebHistory } from "vue-router";
import FormLayout from "../components/FormLayout.vue";
import Fields from "../views/Fields.vue";
import Complete from "../views/Complete.vue";
import FourOhFour from "../views/404.vue";
import AppLayout from "../components/AppLayout.vue"
import Dashboard from "../views/Dashboard.vue";
import Register from "../views/Register.vue";
import Login from "../views/Login.vue";
import store from "../store";
const routes = [
    {
        path: '/',
        redirect: '/app',
    },
    {
        path: '/app',
        name: 'app',
        component: AppLayout,
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: '/dashboard',
                name: 'app.dashboard',
                component: Dashboard,
            },
        ],
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            requiresGuest: true
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: {
            requiresGuest: true
        }
    },
    {
        path:'/form',
        name: 'FormLayout',
        component: FormLayout,
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: '/form',
                name: 'Form',
                component: Fields
            },
            {
                path: '/form/:id',
                name: 'ResubmitForm',
                component: Fields
            },
            {
                path: '/404',
                name: '404',
                component: FourOhFour

            },
            {
                path: '/form/complete',
                name: 'Complete',
                component: Complete
            }
        ]
    }
]

const router = createRouter( {
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.state.user.token) {
        next({name: 'login'})
    } else if (to.meta.requiresGuest && store.state.user.token) {
        next({name: 'app.dashboard'})
    } else {
        next()
    }
})

export default router
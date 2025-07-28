import { createRouter, createWebHistory } from "vue-router";
import FormLayout from "../components/FormLayout.vue";
import Fields from "../views/Fields.vue";

const routes = [
    {
        path: '/',
        redirect: '/form',
        component: FormLayout,
        children: [
            {
                path: '/form',
                name: 'Form',
                component: Fields
            }
        ]
    }
]

const router = createRouter( {
    history: createWebHistory(),
    routes
})

export default router
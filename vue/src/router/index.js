import { createRouter, createWebHistory } from "vue-router";
import FormLayout from "../components/FormLayout.vue";
import Fields from "../views/Fields.vue";
import Complete from "../views/Complete.vue";
import store from "../store";

const routes = [
    {
        path: '/',
        redirect: '/form',
        name: 'FormLayout',
        component: FormLayout,
        children: [
            {
                path: '/form',
                name: 'Form',
                component: Fields
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

export default router
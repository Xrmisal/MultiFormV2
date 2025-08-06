import { createRouter, createWebHistory } from "vue-router";
import FormLayout from "../components/FormLayout.vue";
import Fields from "../views/Fields.vue";
import Complete from "../views/Complete.vue";
import FourOhFour from "../views/404.vue";

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

export default router
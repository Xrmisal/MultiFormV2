import { createRouter, createWebHistory } from "vue-router";
import FormLayout from "../components/FormLayout.vue";
import Fields from "../views/Fields.vue";
import store from "../store";

const routes = [
    {
        path: '/',
        redirect: `/form/${store.state.lead.step}`,
        name: 'FormLayout',
        component: FormLayout,
        children: [
            {
                path: `/form/${store.state.lead.step}`,
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
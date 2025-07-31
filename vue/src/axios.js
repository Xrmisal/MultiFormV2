import axios from 'axios'
import store from './store'

const axiosClient = axios.create({
    baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`
})

export default axiosClient
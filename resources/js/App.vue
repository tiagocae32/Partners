<template>
    <div class="container product-list">
        <div id="message-container"></div>
        <h1 class="text-center">{{ showProducts ? 'Lista de productos' : 'Lista de Categorías' }}</h1>
        <button class="btn btn-primary mb-3" @click="toggleTable">
            {{ showProducts ? 'Ver Categorías' : 'Ver Productos' }}
        </button>
        <button class="btn btn-primary mb-3" @click="openCreateModal">{{ showProducts ? 'Crear Productos' : 'Crear Categorías' }}</button>

        <div v-if="isEditModalOpen" class="modal-overlay">
            <div class="modal-content">
                <h2>Editar Producto</h2>
                <form @submit.prevent="saveProduct">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" v-model="selectedProduct.name" id="name" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="categories">Categorías</label>
                        <multiselect
                            v-model="selectedProduct.categories"
                            :options="categories"
                            label="name"
                            track-by="id"
                            :multiple="true"
                            placeholder="Selecciona categorías"
                            class="form-control"
                        ></multiselect>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <button class="btn btn-secondary" @click="closeEditModal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="isCreateModalOpen" class="modal-overlay">
            <div class="modal-content" v-if="showProducts">
                <h2>Crear Producto</h2>
                <form @submit.prevent="createProduct">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" v-model="newProduct.name" id="name" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <input type="text" v-model="newProduct.description" id="description" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="categories">Categorías</label>
                        <multiselect
                            v-model="newProduct.categories"
                            :options="categories"
                            label="name"
                            track-by="id"
                            :multiple="true"
                            placeholder="Selecciona categorías"
                            class="form-control"
                        ></multiselect>
                    </div>

                    <div class="form-group">
                        <label for="image_url">Imagen</label>
                        <input type="file" @change="handleFileChange($event, 'image_url')" id="image_url" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="cta_url">CTA URL</label>
                        <input type="file" @change="handleFileChange($event, 'cta_url')" id="cta_url" class="form-control" />
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <button class="btn btn-secondary" @click="closeCreateModal">Cancelar</button>
                    </div>
                </form>
            </div>

            <div class="modal-content" v-else>
                <form @submit.prevent="createCategory">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" v-model="newCategory.name" id="name" class="form-control" required />
                    </div>


                    <div class="text-right">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <button class="btn btn-secondary" @click="closeCreateModal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <table v-if="showProducts" class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Categorías</th>
                    <th>Grupo/s</th>
                    <th>Imagen</th>
                    <th>Copiar Json</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="product in products" :key="product.id">
                    <td>{{ product.name }}</td>
                    <td>{{ product.description }}</td>
                    <td>
                        <ul class="list-unstyled">
                            <li v-if="product.categories.length > 0" v-for="category in product.categories" :key="category.id">
                                {{ category.name }}
                            </li>
                            <li v-else>-</li>
                        </ul>
                    </td>
                    <td>
                        <li class="list-unstyled" v-for="category in product.categories" :key="category.id">
                                {{ category.group.name }}
                        </li>
                    </td>
                    <td>
                        <img 
                            v-if="product.image_url" 
                            :src="`http://127.0.0.1:8000/storage/${product.image_url}`" 
                            alt="Imagen del producto" 
                            class="img-thumbnail"
                        />
                        <p v-else class="text-muted" style="margin: 0;">No Image</p>
                    </td>
                    <td> <button class="btn btn-primary" @click="copyJson(product.categories)">Copiar Json</button></td>
                    <td>
                        <button class="btn btn-warning" @click="editProduct(product)">Editar</button>
                        <button class="btn btn-danger" @click="deleteProduct(product.id)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-striped mt-4" v-else>
            <table class="category-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories" :key="category.id">
                        <td>{{ category.name }}</td>
                        <td>
                            <button class="btn btn-warning" @click="editCategory(category)">Editar</button>
                            <button class="btn btn-danger" @click="deleteCategory(category.id)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </table>
    </div>
</template>

<script>
import axios from "axios";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";

export default {
    components: { Multiselect },
    data() {
        return {
            showProducts: true,
            products: [],
            categories: [],
            isEditModalOpen: false,
            isCreateModalOpen: false,
            selectedProduct: {},
            newProduct: {
                name: '',
                description: '',
                categories: [],
                image_url: null,
                cta_url: null,
            },
            newCategory : {
                name : '',
                category_group_id : 1 // default
            }
        };
    },
    methods: {
        toggleTable() {
            this.showProducts = !this.showProducts; 
        },
        async copyJson(categories) {
            const idsCategories = Array.from(new Set(categories.map(category => category.group.id))).join(',')
            const response = await axios.get(`/api/category_groups/${idsCategories}/products`);
            navigator.clipboard.writeText(response.data.url)
                .then(() => {
                    this.showMessage('URL copiada al portapapeles', 'success')
                })
                .catch(err => {
                    console.error("Error al copiar la URL: ", err);
                });
            },      
        showMessage(message, type) {
            const messageContainer = document.getElementById('message-container');
            const messageElement = document.createElement('div');

            messageElement.className = `message-${type}`;
            messageElement.textContent = message;

            messageContainer.appendChild(messageElement);
            setTimeout(() => {
                messageElement.remove();
            }, 3000);
        },
        async fetchProducts() {
            try {
                const response = await axios.get("/api/products");
                this.products = response.data.data;
            } catch (error) {
                console.error("Error fetching products:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get("/api/categories");
                if(response?.data){
                    this.categories = response.data.data;
                }
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async deleteProduct(productId) {
            if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                try {
                    await axios.delete(`/api/products/${productId}`);
                    this.showMessage('Eliminacion ok', 'success')
                    this.fetchProducts(); 
                } catch (error) {
                    console.error("Error al eliminar el producto:", error);
                    this.showMessage('Ocurrió un error al intentar eliminar el producto.', 'danger')
                }
            }
        },
        async deleteCategory(categoryId) {
            if (confirm('¿Estás seguro de que deseas eliminar esta categoria?')) {
                try {
                    await axios.delete(`/api/categories/${categoryId}`);
                    this.showMessage('Eliminacion ok', 'success')
                    this.fetchCategories(); 
                } catch (error) {
                    console.error("Error al eliminar el producto:", error);
                    this.showMessage('Ocurrió un error al intentar eliminar la categoria.', 'danger')
                }
            }
        },
        editProduct(product) {
            this.selectedProduct = { ...product };
            this.isEditModalOpen = true;
        },
        async saveProduct() {
            const payload = {
                ...this.selectedProduct,
                categories: this.selectedProduct.categories.map(category => category.id),
            };

            try {
                await axios.put(`/api/products/${this.selectedProduct.id}`, payload);
                this.fetchProducts();
                this.showMessage('Edicion ok', 'success')
            } catch (error) {
                console.error("Error saving product:", error);
            }finally{
                this.isEditModalOpen = false;
            }
        },
        openCreateModal() {
            this.newProduct = {
                name: '',
                description: '',
                categories: [],
                image_url: null,
                cta_url: null,
            };
            this.isCreateModalOpen = true;
        },
        handleFileChange(event, field) {
            const file = event.target.files[0];
            if (file) {
                this.newProduct[field] = file;
            }
        },
        async createProduct() {
            const formData = new FormData();
            formData.append('name', this.newProduct.name);
            formData.append('description', this.newProduct.description);
            
            if (this.newProduct.image_url) {
                formData.append('image_url', this.newProduct.image_url);
            }
            if (this.newProduct.cta_url) {
                formData.append('cta_url', this.newProduct.cta_url);
            }
            
            this.newProduct.categories.forEach((category, index) => {
                formData.append(`categories[${index}]`, category.id);
            });

            try {
                await axios.post('/api/products', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                this.fetchProducts();
                this.showMessage('Creacion ok', 'success')
            } catch (error) {
                console.error("Error al crear el producto:", error);
            }finally{
                this.closeCreateModal();
            }
        },

        async createCategory(){
            try {
                await axios.post('/api/categories', this.newCategory);
                this.fetchCategories();
                this.showMessage('Creacion ok', 'success')
            } catch (error) {
                console.error("Error al crear el producto:", error);
            }finally{
                this.closeCreateModal();
            }
        },
        closeEditModal() {
            this.isEditModalOpen = false;
        },
        closeCreateModal() {
            this.isCreateModalOpen = false;
        },
    },
    mounted() {
        this.fetchProducts();
        this.fetchCategories();
    },

    
};
</script>

<style scoped>

body {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    color: #333;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: #4a4a4a;
}

h2 {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 15px;
}

.btn {
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 1rem;
    transition: background-color 0.3s;
    margin: 5px;
}

.btn-primary {
    background-color: #007bff;
    color: #fff;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-success {
    background-color: #28a745;
    color: #fff;
}

.btn-success:hover {
    background-color: #218838;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-danger {
    background-color: #dc3545;
    color: #fff;
}

.btn-danger:hover {
    background-color: #c82333;
}

.btn-secondary {
    background-color: #6c757d;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.table th {
    background-color: #f1f1f1;
    font-weight: bold;
}

.table tr:hover {
    background-color: #f8f9fa;
}

.list-unstyled {
    padding-left: 0;
    list-style: none;
}

.list-unstyled li {
    padding: 5px 0;
}

.img-thumbnail {
    width: 60px;
    height: auto;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.text-right {
    text-align: right;
}

input[type="text"],
input[type="file"],
.multiselect {
    width: 100%;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #ced4da;
    margin-bottom: 15px;
    transition: border-color 0.3s;
}

input[type="text"]:focus,
input[type="file"]:focus {
    border-color: #80bdff;
    outline: none;
}


#message-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 300px;
}

.message-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    animation: fade-in 0.5s;
}

.message-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    animation: fade-in 0.5s;
}

.message-info {
    background-color: #cce5ff;
    color: #004085;
    border: 1px solid #b8daff;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    animation: fade-in 0.5s;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-success::before {
    content: '✔️';
    margin-right: 10px;
}

.message-error::before {
    content: '❌';
    margin-right: 10px;
}

.message-info::before {
    content: 'ℹ️';
    margin-right: 10px;
}


</style>

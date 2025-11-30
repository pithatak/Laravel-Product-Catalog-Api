<script setup>
import { ref, onMounted, watch } from 'vue'

// ----- DATA -----
const products = ref([])
const loading = ref(true)

// global filters
const search = ref('')
const category = ref('')
const manufacturer = ref('')
const minPrice = ref('')
const maxPrice = ref('')

// dynamic attributes filters
const minCapacity = ref('')
const maxCapacity = ref('')

const minPower = ref('')
const maxPower = ref('')

const connectorType = ref('')

// lists
const manufacturers = ref([])
const connectorTypes = ref([])


// ----- DEBOUNCE -----
function debounce(fn, delay = 400) {
    let timer = null
    return (...args) => {
        clearTimeout(timer)
        timer = setTimeout(() => fn(...args), delay)
    }
}

const debouncedLoad = debounce(loadProducts)


// ----- LOAD PRODUCTS -----
async function loadProducts() {
    loading.value = true

    const params = new URLSearchParams()

    // global filters
    if (search.value) params.append('search', search.value)
    if (category.value) params.append('category', category.value)
    if (manufacturer.value) params.append('manufacturer', manufacturer.value)
    if (minPrice.value) params.append('min_price', minPrice.value)
    if (maxPrice.value) params.append('max_price', maxPrice.value)

    // category filters
    if (category.value === 'battery') {
        if (minCapacity.value) params.append('min_capacity', minCapacity.value)
        if (maxCapacity.value) params.append('max_capacity', maxCapacity.value)
    }

    if (category.value === 'panel') {
        if (minPower.value) params.append('min_power', minPower.value)
        if (maxPower.value) params.append('max_power', maxPower.value)
    }

    if (category.value === 'connector' && connectorType.value) {
        params.append('connector_type', connectorType.value)
    }

    const res = await fetch(`/api/products?${params.toString()}`)
    const data = await res.json()

    products.value = data
    loading.value = false

    // dynamic lists
    manufacturers.value = [...new Set(data.map(p => p.manufacturer))].sort()

    connectorTypes.value = [
        ...new Set(
            data
                .filter(p => p.category === 'connector')
                .map(p => p.attributes?.[0]?.value)
        )
    ].filter(Boolean)
}


// ----- WATCH FILTERS -----
watch(
    [
        search, category, manufacturer,
        minPrice, maxPrice,
        minCapacity, maxCapacity,
        minPower, maxPower,
        connectorType
    ],
    debouncedLoad
)

onMounted(loadProducts)
</script>


<template>
    <div class="max-w-7xl mx-auto px-4 py-6">

        <h1 class="text-3xl font-bold mb-6">Product Catalog</h1>

        <!-- FILTER CARD -->
        <div class="bg-white p-6 rounded shadow mb-6 space-y-4">

            <!-- Main filters -->
            <div class="grid md:grid-cols-3 gap-4">

                <input v-model="search" type="text"
                       placeholder="Search by name, manufacturer, description..."
                       class="border p-2 rounded w-full">

                <select v-model="category"
                        class="border p-2 rounded w-full">
                    <option value="">All Categories</option>
                    <option value="battery">Batteries</option>
                    <option value="panel">Solar Panels</option>
                    <option value="connector">Connectors</option>
                </select>

                <select v-model="manufacturer"
                        class="border p-2 rounded w-full">
                    <option value="">All Manufacturers</option>
                    <option v-for="m in manufacturers" :key="m">{{ m }}</option>
                </select>

            </div>

            <!-- Price range -->
            <div class="grid md:grid-cols-2 gap-4">
                <input v-model="minPrice" type="number"
                       placeholder="Min Price"
                       class="border p-2 rounded w-full">

                <input v-model="maxPrice" type="number"
                       placeholder="Max Price"
                       class="border p-2 rounded w-full">
            </div>

            <!-- Category-specific filters -->
            <div v-if="category === 'battery'" class="grid md:grid-cols-2 gap-4">
                <input v-model="minCapacity" type="number" placeholder="Min Capacity (Ah)"
                       class="border p-2 rounded w-full">
                <input v-model="maxCapacity" type="number" placeholder="Max Capacity (Ah)"
                       class="border p-2 rounded w-full">
            </div>

            <div v-if="category === 'panel'" class="grid md:grid-cols-2 gap-4">
                <input v-model="minPower" type="number" placeholder="Min Power Output (W)"
                       class="border p-2 rounded w-full">
                <input v-model="maxPower" type="number" placeholder="Max Power Output (W)"
                       class="border p-2 rounded w-full">
            </div>

            <div v-if="category === 'connector'" class="grid md:grid-cols-1 gap-4">
                <select v-model="connectorType" class="border p-2 rounded w-full">
                    <option value="">All Types</option>
                    <option v-for="t in connectorTypes" :key="t">{{ t }}</option>
                </select>
            </div>

        </div>


        <!-- TABLE -->
        <div class="bg-white rounded shadow overflow-x-auto">

            <table class="min-w-full border-collapse">
                <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2 text-left">ID</th>
                    <th class="border p-2 text-left">Name</th>
                    <th class="border p-2 text-left">Manufacturer</th>
                    <th class="border p-2 text-left">Price</th>
                    <th class="border p-2 text-left">Category</th>
                    <th class="border p-2 text-left">Description</th>
                    <th class="border p-2 text-left">Attributes</th>
                </tr>
                </thead>

                <tbody>

                <tr v-if="loading">
                    <td colspan="7" class="p-4 text-center">Loading...</td>
                </tr>

                <tr v-for="p in products" :key="p.id" class="hover:bg-gray-50">
                    <td class="border p-2">{{ p.id }}</td>
                    <td class="border p-2">{{ p.name }}</td>
                    <td class="border p-2">{{ p.manufacturer }}</td>
                    <td class="border p-2">{{ p.price }}$</td>
                    <td class="border p-2">{{ p.category }}</td>
                    <td class="border p-2 max-w-[350px]">{{ p.description }}</td>

                    <td class="border p-2">
                        <span v-if="p.category === 'battery'">
                            {{ p.attributes[0]?.value }} Ah
                        </span>

                        <span v-else-if="p.category === 'panel'">
                            {{ p.attributes[0]?.value }} W
                        </span>

                        <span v-else-if="p.category === 'connector'">
                            {{ p.attributes[0]?.value }}
                        </span>

                        <span v-else>-</span>
                    </td>
                </tr>

                </tbody>
            </table>

        </div>

    </div>
</template>

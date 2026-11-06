<template>
    <div class="header-search-2">
        <form @submit.prevent="handleSubmit">
            <div class="input-wrapper">
                <input 
                    type="text" 
                    v-model="searchQuery" 
                    @input="debounceFetchSuggestions" 
                    placeholder="Search here..." 
                />
                <!-- Clear Button -->
                <span 
                    v-if="searchQuery" 
                    class="clear-btn" 
                    @click="clearInput"
                >
                    &times;
                </span>
            </div>
            <!-- <button type="submit">
                <span><i class="icon-search"></i></span>
            </button> -->
        </form>
        <!-- Suggestion Dropdown -->
        <ul v-if="suggestions.length" class="search-suggestions">
            <li 
                v-for="(suggestion, index) in suggestions" 
                :key="index" 
                class="suggestion-item"
            >
                <router-link :to="{ name: 'product-details', params: { id: suggestion.id } }" @click.native="clearSuggestions" class="suggestion-link">
                    <img width="50px" :src="'/' + suggestion.image" :alt="suggestion.name" loading="lazy" />
                    <span class="suggestion-text">{{ suggestion.name }}</span>
                </router-link>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    name: "SearchProduct",
    data() {
        return {
            searchQuery: '',
            suggestions: [],
            debounceTimeout: null,
        };
    },
    methods: {
        async fetchSuggestions() {
            if (this.searchQuery.trim().length < 1) {
                this.suggestions = [];
                return;
            }
            try {
                const response = await fetch(`/api/get-search-products?query=${this.searchQuery}`);
                const data = await response.json();
                console.log(data);
                // this.suggestions = data.products.map(product => product.name);
                this.suggestions = data.products;
            } catch (error) {
                console.error("Error fetching suggestions:", error);
            }
        },
        debounceFetchSuggestions() {
            // Debounce to limit API calls
            clearTimeout(this.debounceTimeout);
            this.debounceTimeout = setTimeout(() => {
                this.fetchSuggestions();
            }, 300); // Wait 300ms before calling API
        },
        selectSuggestion(suggestion) {
            // this.searchQuery = suggestion;
            // this.suggestions = [];
        },
        handleSubmit() {
            console.log("Search submitted:", this.searchQuery);
        },
        clearInput() {
            this.searchQuery = '';
            this.suggestions = [];
        },
        clearSuggestions() {
            this.searchQuery = '';
            this.suggestions = [];
        },
    },
};
</script>
<style scoped>
    .header-search-2 {
        position: relative;
    }
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    input[type="text"] {
        width: 100%;
        padding-right: 30px;
        box-sizing: border-box;
    }
    .clear-btn {
        position: absolute;
        right: 20px;
        cursor: pointer;
        font-size: 20px;
        color: #888;
        user-select: none;
    }

    .clear-btn:hover {
        color: #000;
    }
    .search-suggestions {
        list-style: none;
        margin: 0;
        padding: 0;
        border: 1px solid #ccc;
        background: #fff;
        position: absolute;
        width: 100%;
        z-index: 10;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }
    .search-suggestions::-webkit-scrollbar {
        width: 8px;
        background-color: #f1f1f1;
    }
    .search-suggestions::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 4px;
    }
    .search-suggestions::-webkit-scrollbar-thumb:hover {
        background-color: #555; 
    }
    .suggestion-item {
        border-bottom: 1px solid #f0f0f0;
        margin-top: 0px;
    }
    .suggestion-item:last-child {
        border-bottom: none;
    }
    .suggestion-link {
        display: flex;
        align-items: center;
        padding: 10px;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
    }
    .suggestion-link:hover {
        background-color: #f0f0f0;
    }
    .suggestion-link img {
        margin-right: 10px;
        border-radius: 5px;
    }
    .suggestion-text {
        font-size: 14px;
        color: #333;
    }
</style>

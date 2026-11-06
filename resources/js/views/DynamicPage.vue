<template>
  <div>
    <!-- header area -->
    <Header />

    <div id="dynamic_page" class="animate__animated animate__fadeInUp">
      <div class="sp_header bg-white p-3">
        <div class="container custom_container">
          <div class="row">
            <div class="col-12">
              <ul>
                <li class="d-inline-block font-weight-bolder">
                  <router-link :to="{ name: 'home' }">home</router-link>
                </li>
                <li class="d-inline-block hr_ font-weight-bolder">
                  <span>{{ pageLoading ? "Loading..." : page?.title }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Page content -->
      <div class="container mx-auto">
        <!-- Skeleton Loader -->
        <div v-if="pageLoading" class="space-y-3">
          <div class="h-8 bg-gray-200 animate-pulse w-1/3 rounded"></div>
          <div class="h-4 bg-gray-200 animate-pulse w-full rounded"></div>
          <div class="h-4 bg-gray-200 animate-pulse w-5/6 rounded"></div>
          <div class="h-4 bg-gray-200 animate-pulse w-2/3 rounded"></div>
        </div>

        <!-- Actual Page -->
        <div v-else>
          <h1 class="text-2xl font-bold mb-4">{{ page?.title }}</h1>
          <div v-html="page?.content"></div>
        </div>
      </div>
    </div>

    <!-- footer -->
    <Footer />
  </div>
</template>

<script>
import Header from "../components/Header.vue";
import Footer from "../components/Footer.vue";
import { mapGetters, mapActions } from "vuex";
import { useHead } from "@vueuse/head";

export default {
  name: "DynamicPage",
  computed: {
    ...mapGetters("home", ["page", "pageLoading"]),
  },
  watch: {
    "$route.params.slug": {
      immediate: true,
      handler(slug) {
        this.fetchPage(slug);
      },
    },
    page(newPage) {
      if (newPage) {
        useHead({
          title: newPage.title,
          meta: [
            {
              name: "description",
              content: newPage.meta_description || newPage.content?.slice(0, 150),
            },
          ],
        });
      }
    },
  },
  methods: {
    ...mapActions("home", ["fetchPage"]),
  },
  components: { Header, Footer },
  mounted() {
    if (window.innerWidth > 991) {
      $("#sp_vertical_menu .vertical_menu").insertBefore(
        "#sp_header_top .col-lg-9.text-left"
      );
    }
  },
};
</script>

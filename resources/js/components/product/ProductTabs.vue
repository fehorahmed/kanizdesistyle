<template>
  <div class="product-tabs mt-6">
    <!-- Tab buttons -->
    <div class="flex justify-center mb-4 border-b border-gray-200">
      <button
        :class="activeTab === 'description' ? activeClass : inactiveClass"
        @click="activeTab = 'description'"
      >
        DESCRIPTIONS
      </button>
      <button
        :class="activeTab === 'reviews' ? activeClass : inactiveClass"
        @click="activeTab = 'reviews'"
      >
        CUSTOMER REVIEWS ({{ summary_reviews.length }})
      </button>
    </div>

    <!-- Tab content -->
    <div class="tab-content mx-auto">
      <!-- Description Tab -->
      <div v-if="activeTab === 'description'">
        <div class="overflow-x-auto">
          <div class="descriptions" v-html="product.description"></div>
        </div>
      </div>

      <!-- Reviews Tab -->
      <div v-else-if="activeTab === 'reviews'" class="reviews">
        
        <!-- Review Summary -->
        <div class="review-main-area mb-6 p-4 border rounded bg-gray-50">
          <div class="review-main-cus">
            <h1 class="text-lg font-bold mb-2">Customer Review ({{ summary_reviews.length }})</h1>
            <div class="review-main-cus-child flex items-center gap-4 mb-3">
              <h2 class="text-2xl font-bold">{{ averageRating.toFixed(1) }}</h2>
              <div class="user-review-rating flex items-center gap-1 text-yellow-500">
                <ul class="flex">
                  <li v-for="i in 5" :key="i">
                    <i :class="i <= Math.round(averageRating) ? 'fas fa-star' : 'far fa-star'"></i>
                  </li>
                </ul>
              </div>
              <p class="text-gray-500">based on {{ summary_reviews.length }} reviews</p>
            </div>

            <!-- Rating bars -->
            <div class="rating-line-bars space-y-1">
              <div class="rating-line-bar flex items-center gap-2" v-for="label in ratingLabels" :key="label.text">
                <p class="w-24 text-gray-700">{{ label.text }}</p>
                <div class="first-bar flex-1 bg-gray-200 h-2 rounded overflow-hidden">
                  <div class="first-bar-child bg-primary_color h-2" :style="{ width: label.width + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Review Form (only if logged in) -->
        <div v-if="isLoggedIn" class="mt-6 p-4 border rounded bg-gray-50">
          <h3 class="font-bold mb-3">Write a Review</h3>
          <form @submit.prevent="submitReviewForm">
            <!-- Rating -->
            <div class="mb-3">
              <label class="block mb-1 font-medium">Rating</label>
              <select
                v-model="newReview.rating"
                class="border rounded px-3 py-2 w-full"
              >
                <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                <option value="4">⭐⭐⭐⭐ - Good</option>
                <option value="3">⭐⭐⭐ - Average</option>
                <option value="2">⭐⭐ - Poor</option>
                <option value="1">⭐ - Very Bad</option>
              </select>
            </div>

            <!-- Comment -->
            <div class="mb-3">
              <label class="block mb-1 font-medium">Comment</label>
              <textarea
                v-model="newReview.comment"
                class="border rounded px-3 py-2 w-full"
                rows="3"
                placeholder="Write your review..."
              ></textarea>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              class="bg-primary_color text-white px-4 py-2 rounded hover:bg-primary_color/90"
              :disabled="reviewLoading"
            >
              {{ reviewLoading ? "Submitting..." : "Submit Review" }}
            </button>
          </form>
        </div>

        <!-- Show login prompt if not logged in -->
        <div v-else class="mt-6 text-center text-gray-600">
          <p>
            You must
            <router-link :to="{ name: 'login' }" class="text-primary_color">
              login
            </router-link>
            to write a review.
          </p>
        </div>
        <!-- Existing Reviews -->
        <div v-if="reviews.length" class="mt-6">
          <div
            v-for="(review, index) in reviews"
            :key="index"
            class="user-review mb-4 border-b border-gray-200 pb-4"
          >
            <div class="main-review flex gap-4">
              <!-- User Data -->
              <div class="user-data flex-shrink-0">
                <div class="user-img user-img-rev w-12 h-12 rounded-full overflow-hidden">
                  <img
                    src="../../../../img/user_low.png"
                    alt="avatar"
                    class="w-full h-full object-cover"
                  />
                </div>
              </div>

              <!-- Review Content -->
              <div class="flex-1">
                <div class="user-name font-bold">
                  {{ review.user }}
                  <small class="ml-2 text-gray-500 font-normal">
                    <i class="fa fa-star"></i> Verified Purchase
                  </small>
                </div>
                <div class="user-review-rating flex items-center gap-2 mt-1">
                  <ul class="flex text-yellow-500">
                    <li v-for="i in 5" :key="i">
                      <i :class="i <= review.rating ? 'fas fa-star' : 'far fa-star'"></i>
                    </li>
                  </ul>
                  <span class="text-gray-600 ml-2">{{ review.rating.toFixed(1) }}</span>
                </div>

                <div class="user-review-data mt-2 text-gray-700">
                  <p>{{ review.comment }}</p>
                  <!-- Optional review images -->
                  <div v-if="review.images?.length" class="review-product-image-area flex gap-2 mt-2">
                    <div
                      v-for="(img, imgIndex) in review.images"
                      :key="imgIndex"
                      class="review-product-img-main w-20 h-20 overflow-hidden rounded"
                    >
                      <img
                        :src="img"
                        alt="review image"
                        class="w-full h-full object-cover cursor-pointer"
                        @click="openLightbox(img, review.images)"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Load More Button -->
          <div v-if="reviewPage < reviewLastPage" class="text-center mt-4">
            <button
              @click="loadMoreReviews"
              class="bg-primary_color text-white px-4 py-2 rounded hover:bg-primary_color/90"
              :disabled="reviewLoading"
            >
              {{ reviewLoading ? 'Loading...' : 'Load More Reviews' }}
            </button>
          </div>

        </div>
        
        
        <div v-else class="text-gray-500">No reviews yet.</div>

      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "ProductTabs",
  props: {
    product: { type: Object, required: true },
  },
  data() {
    return {
      activeTab: "description",
      activeClass:
        "px-4 py-2 font-bold text-primary_color border-b-2 border-primary_color focus:outline-none",
      inactiveClass:
        "px-4 py-2 text-gray-500 hover:text-primary_color focus:outline-none",
      newReview: { rating: 5, comment: "" },
    };
  },
  computed: {
    ...mapGetters("auth", ["isLoggedIn"]),
    ...mapGetters("product_detail", ["summary_reviews", "reviews", "reviewLoading", "reviewPage", "reviewLastPage"]),
    averageRating() {
      if (!this.summary_reviews.length) return 0;
        const total = this.summary_reviews.reduce((sum, r) => sum + r.rating, 0);
        return total / this.summary_reviews.length;
    },

    // Rating labels and width for bars
    ratingLabels() {
      const counts = { Excellent: 0, Good: 0, Average: 0, "Below Average": 0, Poor: 0 };
      this.summary_reviews.forEach(r => {
        if (r.rating >= 5) counts.Excellent++;
        else if (r.rating >= 4) counts.Good++;
        else if (r.rating >= 3) counts.Average++;
        else if (r.rating >= 2) counts["Below Average"]++;
        else counts.Poor++;
      });
      const total = this.summary_reviews.length || 1;
      return Object.keys(counts).map(label => ({
        text: label,
        width: (counts[label] / total) * 100
      }));
    }
  },
  methods: {
    ...mapActions("product_detail", ["fetchSummaryReviews", "fetchReviews", "submitReview"]),
    async submitReviewForm() {
      if (!this.newReview.comment.trim()) {
        alert("Please write a comment before submitting.");
        return;
      }
      try {
        const response = await this.submitReview({
          productId: this.product.id,
          reviewData: this.newReview,
        });
        this.newReview = { rating: 5, comment: "" };
      } catch (err) {
        alert("Failed to submit review.");
      }
    },
    async loadMoreReviews() {
      if (this.reviewPage >= this.reviewLastPage) return;
      await this.fetchReviews({ productId: this.product.id, page: this.reviewPage + 1 });
    },
    openLightbox(img, images) {
      // Implement lightbox logic
    },
  },
  watch: {
    product: {
      immediate: true,
      handler(newProduct) {
        if (newProduct?.id) {
          if (newProduct?.id) this.fetchReviews({ productId: newProduct.id, page: 1 });
        }
        if (newProduct?.id) {
          if (newProduct?.id) this.fetchSummaryReviews({ productId: newProduct.id });
        }
      },
    },
  },
};
</script>

<style scoped>
.product-tabs button {
  transition: all 0.3s ease;
}
</style>

import { createRouter, createWebHistory } from 'vue-router';
import store from './store';

// Pages
import NotFound from './components/NotFound.vue';
import BlogPage from './views/BlogPage.vue';
import CheckoutPage from './views/CheckoutPage.vue';
import ComparePage from './views/ComparePage.vue';
import DynamicPage from './views/DynamicPage.vue';
import ForgotPage from './views/ForgotPage.vue';
import GalleryPage from './views/GalleryPage.vue';
import HomePage from './views/HomePage.vue';
import LoginPage from './views/LoginPage.vue';
import OrderSuccess from './views/OrderSuccess.vue';
import ProductDetailPage from './views/ProductDetailPage.vue';
import ProductsPage from './views/ProductsPage.vue';
import RegisterPage from './views/RegisterPage.vue';
import ResetPasswordPage from './views/ResetPasswordPage.vue';
import WishlistPage from './views/WishlistPage.vue';

const APP_NAME = "Kaniz's Desi Style";

const routes = [
  { path: '/', component: HomePage, name: 'home', meta: { title: `Home | ${APP_NAME}`, description: 'Welcome to Ecommerce online store.' } },
  { path: '/pages/:slug', component: DynamicPage, name: 'dynamic_pages', meta: { title: 'Loading...', description: '' } },
  { path: '/products/:slug?', component: ProductsPage, name: 'products', meta: { title: `Products | ${APP_NAME}`, description: 'Explore our product catalog.' },props: (route) => ({ slug: route.params.slug || null }), },
  { path: '/product/details/:slug', component: ProductDetailPage, name: 'product_details', meta: { title: 'Loading...', description: '' } },
  { path: '/blog', component: BlogPage, name: 'blog', meta: { title: `Blog | ${APP_NAME}`, description: 'Read the latest tech articles.' } },
  { path: '/gallery', component: GalleryPage, name: 'gallery', meta: { title: `Gallery | ${APP_NAME}`, description: 'Browse our gallery.' } },
  // { path: '/about-us', component: AboutPage, name: 'about', meta: { title: `About | ${APP_NAME}`, description: 'Learn more about us.' } },
  { path: '/login', component: LoginPage, name: 'login', meta: { title: `Login | ${APP_NAME}`, guestOnly: true } },
  { path: '/register', component: RegisterPage, name: 'register', meta: { title: `Register | ${APP_NAME}`, guestOnly: true } },
  { path: '/forgot-password', component: ForgotPage, name: 'forgot-password', meta: { title: `Forgot Password | ${APP_NAME}` } },
  { path: '/reset-password/:token', component: ResetPasswordPage, name: 'reset-password', meta: { title: `Reset Password | ${APP_NAME}` } },
  { path: '/compare', component: ComparePage, name: 'compare', meta: { title: `Compare | ${APP_NAME}` } },
  {
    path: '/account',
    component: () => import('@/views/AccountLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'account.dashboard', component: () => import('@/views/account/Dashboard.vue'), meta: { title: `Dashboard | ${APP_NAME}` } },
      { path: 'orders', name: 'account.orders', component: () => import('@/views/account/Orders.vue'), meta: { title: `Orders | ${APP_NAME}` } },
      { path: 'profile', name: 'account.profile', component: () => import('@/views/account/Profile.vue'), meta: { title: `Profile | ${APP_NAME}` } },
      { path: 'password', name: 'account.password', component: () => import('@/views/account/Password.vue'), meta: { title: `Change Password | ${APP_NAME}` } }
    ]
  },
  { path: '/wishlist', component: WishlistPage, name: 'wishlist', meta: { title: `Wishlist | ${APP_NAME}`, requiresAuth: true } },
  { path: '/checkout', component: CheckoutPage, name: 'checkout', meta: { title: `Checkout | ${APP_NAME}`, requiresAuth: true } },
  { path: '/order-success/:id', component: OrderSuccess, name: 'order.success', meta: { title: `Order Success | ${APP_NAME}`, requiresAuth: true } },
  { path: '/:pathMatch(.*)*', component: NotFound, name:'NotFound', meta: { title: `404 | ${APP_NAME}` } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

// Auth guard
router.beforeEach(async (to, from, next) => {
  if (!store.getters['auth/user']) {
    await store.dispatch('auth/fetchUser');
  }

  const isLoggedIn = store.getters['auth/isLoggedIn'];

  if (to.meta.requiresAuth && !isLoggedIn) {
    next({ name: 'login', query: { redirect: to.fullPath } });
  } else if (to.meta.guestOnly && isLoggedIn) {
    next({ name: 'account.dashboard' });
  } else {
    next();
  }
});

// SEO: Update document title & description after each route
router.afterEach((to) => {
  // Set document title
  document.title = to.meta.title || APP_NAME;

  // Set meta description dynamically
  let descriptionTag = document.querySelector('meta[name="description"]');
  if (!descriptionTag) {
    descriptionTag = document.createElement('meta');
    descriptionTag.setAttribute('name', 'description');
    document.head.appendChild(descriptionTag);
  }
  descriptionTag.setAttribute('content', to.meta.description || '');
});

export default router;

import path from 'node:path';

import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';
import { ConfigEnv, loadEnv, UserConfig } from 'vite';
import { viteMockServe } from 'vite-plugin-mock';
import svgLoader from 'vite-svg-loader';
import viteCompression from "vite-plugin-compression";
const CWD = process.cwd();


// https://vitejs.dev/config/
export default ({ mode }: ConfigEnv): UserConfig => {
  const { VITE_BASE_URL, VITE_API_URL_PREFIX } = loadEnv(mode, CWD);
  return {
    base: "./",
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './src'),
      },
    },

    css: {
      preprocessorOptions: {
        less: {
          modifyVars: {
            hack: `true; @import (reference) "${path.resolve('src/style/variables.less')}";`,
          },
          math: 'strict',
          javascriptEnabled: true,
        },
      },
    },

    plugins: [
      vue(),
      vueJsx(),
      // gzip压缩
      viteCompression(),
      viteMockServe({
        mockPath: 'mock',
        enable: true,
      }),
      svgLoader(),
    ],

    // 打包配置 - 去除打印和断点
    esbuild: {
      pure: mode == 'site' ? ["console.log", "debugger"] : []
    },

    server: {
      port: 8000,
      // ip 地址
      host: '0.0.0.0',
      // 跨域配置 - 开发服务器
      proxy: {
        '/api': {
          // target: 'http://192.168.15.168:8091',
          // target: 'http://192.168.15.168:10000',
          // 郭
          target: 'http://caigou2.yuanmapai.net.local/',
          // target: 'http://api.member.iflight.cn.local/',
          // 测试环境
          // target: 'http://192.168.15.173:8091/',
          changeOrigin: true,
          // rewrite: (path) => path.replace(/^\/api/, '/api')
        }
      }
    },
  };
};

import { defineStore } from 'pinia';

import { BaseInfo, UploadInfo, Product, CreatePurchaseOrder } from '@/api/model/purchaseModel';

export const usePurchaseStore = defineStore('purchase', {
  state: () => {
    return {
      // 基本信息
      baseInfo: {
        enter_way: 'excel',
        name: '',
        description: ''
      } as BaseInfo,

      // 上传信息
      uploadInfo: {
        name: '',
        url: '',
        size: 0,
        uploadTime: '',
        path: '',
      } as UploadInfo,

      // 商品信息
      productList: [] as Product[],

      // is finished
      is_finished: false,
    }
  },
  getters: {

    getBaseInfo(): BaseInfo {
      return this.baseInfo
    },
    getUploadInfo(): UploadInfo {
      return this.uploadInfo
    },
    getProductList(): Product[] {
      return this.productList
    },
    getFinished(): boolean {
      return this.is_finished
    },

  },
  actions: {

    // set schema
    setBaseInfo(baseInfo: BaseInfo): void {
      this.baseInfo = baseInfo
    },

    setUploadInfo(uploadInfo: UploadInfo): void {
      this.uploadInfo = uploadInfo
    },

    setProductList(productList: Array<Product>): void {
      this.productList = productList
    },
    // add structure
    addProduct(product: Product): void {
      this.productList.push(product)
    },
  },
  persist: true,
});
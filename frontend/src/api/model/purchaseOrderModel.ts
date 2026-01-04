export interface PurchaseOrderModel {
  id?: number;
  number?: string;
  name?: string;
  status?: number;
  created_at?: string;
  updated_at?: string;
}

export interface searchParameter {
  purchaseNumber?: string;
  purchaseName?: string;
  purchaseStatus?: number;
  purchaseStartedAt?: string;
  purchaseEndedAt?: string;
  page: number;
  pageSize: number;
  sortBy: string;
  descending: boolean;
}





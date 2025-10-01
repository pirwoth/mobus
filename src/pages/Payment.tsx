import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { ArrowLeft, CreditCard, Smartphone } from "lucide-react";

const paymentMethods = [
  {
    id: "card",
    name: "Credit/Debit Card",
    icon: CreditCard,
  },
  {
    id: "mobile",
    name: "Mobile Money",
    icon: Smartphone,
  },
];

const Payment = () => {
  const navigate = useNavigate();
  const [selectedMethod, setSelectedMethod] = useState<string>("");

  const handleConfirmPayment = () => {
    if (selectedMethod) {
      navigate("/ticket");
    }
  };

  return (
    <div className="min-h-screen bg-background">
      <header className="px-6 py-4 border-b border-border flex items-center gap-4">
        <Button
          variant="ghost"
          size="icon"
          onClick={() => navigate("/seats")}
          className="rounded-full"
        >
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <h1 className="text-xl font-semibold">Payment Method</h1>
      </header>

      <main className="px-6 py-8">
        <div className="space-y-4 mb-8">
          {paymentMethods.map((method) => {
            const Icon = method.icon;
            const isSelected = selectedMethod === method.id;

            return (
              <button
                key={method.id}
                onClick={() => setSelectedMethod(method.id)}
                className={`w-full bg-card rounded-ios-lg shadow-ios p-6 text-left transition-all ${
                  isSelected ? "ring-2 ring-foreground" : ""
                }`}
              >
                <div className="flex items-center gap-4">
                  <div
                    className={`w-12 h-12 rounded-full flex items-center justify-center ${
                      isSelected ? "bg-foreground text-background" : "bg-muted"
                    }`}
                  >
                    <Icon className="h-6 w-6" />
                  </div>
                  <div className="flex-1">
                    <h3 className="font-semibold text-lg">{method.name}</h3>
                  </div>
                  <div
                    className={`w-6 h-6 rounded-full border-2 flex items-center justify-center ${
                      isSelected
                        ? "border-foreground bg-foreground"
                        : "border-border"
                    }`}
                  >
                    {isSelected && (
                      <div className="w-3 h-3 rounded-full bg-background" />
                    )}
                  </div>
                </div>
              </button>
            );
          })}
        </div>

        <div className="bg-muted rounded-ios-lg p-5 mb-6">
          <div className="flex items-center justify-between mb-2">
            <span className="text-muted-foreground">Subtotal</span>
            <span className="font-semibold">$135.00</span>
          </div>
          <div className="flex items-center justify-between mb-3 pb-3 border-b border-border">
            <span className="text-muted-foreground">Service Fee</span>
            <span className="font-semibold">$5.00</span>
          </div>
          <div className="flex items-center justify-between">
            <span className="text-lg font-semibold">Total</span>
            <span className="text-2xl font-bold">$140.00</span>
          </div>
        </div>

        <Button
          onClick={handleConfirmPayment}
          disabled={!selectedMethod}
          className="w-full rounded-ios h-14 text-base font-medium"
        >
          Confirm Payment
        </Button>
      </main>
    </div>
  );
};

export default Payment;

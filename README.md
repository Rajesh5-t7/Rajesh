# E-Sports Tournament App

A production-ready mobile-first E-Sports Tournament application with separate user and admin panels, built with HTML, TailwindCSS, and Firebase.

## 🚀 Features

### User Panel (`user.html`)
- **Authentication**: Email/password login and signup
- **Wallet System**: 
  - View balance with gradient cards
  - Add money via QR code and transaction ID
  - Withdraw money via UPI ID
  - Transaction history with color-coded credits/debits
- **Tournament System**:
  - Browse upcoming tournaments
  - Join tournaments (one-time only per tournament)
  - View joined tournaments (Upcoming/Live/Completed)
  - Live tournaments show Room ID and Password
  - Completed tournaments show results (Winner/Participated)
- **Profile Management**: Username, email, password reset, logout

### Admin Panel (`admin.html`)
- **Dashboard**: Statistics for users, tournaments, prize money, and revenue
- **Tournament Management**:
  - Create tournaments with full details
  - Manage tournament status (Upcoming → Live → Completed)
  - Set Room ID and Password for live tournaments
  - View participants
  - Declare winners and credit prize money
- **Wallet Requests**:
  - Approve/reject add money requests
  - Approve/reject withdraw requests
- **Settings**: QR code URL management, password reset, user panel access

## 🛠️ Tech Stack

- **Frontend**: HTML5, TailwindCSS (CDN), Font Awesome (CDN)
- **Backend**: Firebase Authentication + Realtime Database
- **JavaScript**: Vanilla JavaScript (no frameworks)
- **Design**: Mobile-first responsive design

## 📱 Mobile-First Design

- **Layout**: Centered max-width container (`max-w-md mx-auto`)
- **Navigation**: Sticky header + fixed bottom navigation
- **Theme**: Gray background with white cards and gradient accents
- **Colors**:
  - Primary: Blue → Teal gradient
  - Success: Green → Teal gradient  
  - Danger: Red → Orange gradient
  - Cards: Purple gradient

## 🔧 Firebase Setup

### Configuration
Replace the placeholder values in both files:

```javascript
const firebaseConfig = {
  apiKey: "YOUR_API_KEY",
  authDomain: "YOUR_AUTH_DOMAIN", 
  databaseURL: "YOUR_DATABASE_URL",
  projectId: "YOUR_PROJECT_ID",
  storageBucket: "YOUR_STORAGE_BUCKET",
  messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
  appId: "YOUR_APP_ID"
};
```

### Database Structure

```
/users/{uid}:
  username: string
  email: string
  wallet: number
  transactions: array

/tournaments/{tid}:
  title: string
  game: string
  entry_fee: number
  prize_pool: number
  match_time: timestamp
  status: "Upcoming" | "Live" | "Completed"
  thumbnail: string (URL)
  room_id: string
  room_password: string
  participants/{uid}: {username, result}

/addMoneyRequests/{reqId}:
  uid: string
  amount: number
  txnId: string
  status: "pending" | "approved" | "rejected"
  timestamp: number

/withdrawRequests/{reqId}:
  uid: string
  upi_id: string
  amount: number
  status: "pending" | "approved" | "rejected"
  timestamp: number

/settings:
  qrCodeUrl: string (URL)
```

## 🎮 Supported Games

- Free Fire
- BGMI (Battlegrounds Mobile India)
- Call of Duty Mobile
- PUBG Mobile
- Clash Royale
- Clash of Clans

## 💰 Wallet System

### Add Money Flow
1. User clicks "Add Money"
2. QR code is displayed (set by admin in settings)
3. User pays manually and enters Transaction ID
4. Request is saved with "pending" status
5. Admin approves → wallet is credited

### Withdraw Flow
1. User enters amount and UPI ID
2. Request is saved with "pending" status
3. Admin approves → balance is deducted
4. Admin sends money manually to user

## 🏆 Tournament System

### User Experience
- Browse upcoming tournaments on home page
- Join tournaments (one-time only, balance check)
- View joined tournaments in "My Tournaments"
- Live tournaments show room details
- Completed tournaments show results

### Admin Management
- Create tournaments with all details
- Change status: Upcoming → Live → Completed
- Set room details for live tournaments
- Declare winners and credit prize money
- View participant lists

## 📱 Mobile UI Components

### Cards
- White background with shadow and rounded corners
- Gradient accents for actions
- Responsive image thumbnails
- Status indicators with color coding

### Navigation
- Sticky header with app title and wallet balance
- Fixed bottom navigation with icons
- Tab-based content switching
- Modal overlays for forms

### Forms
- Clean input fields with focus states
- Validation and error handling
- Loading states and success feedback
- Mobile-optimized touch targets

## 🔒 Security Features

- Firebase Authentication for user management
- Real-time database with proper rules
- Input validation and sanitization
- Secure password reset functionality
- Admin-only access controls

## 🚀 Getting Started

1. **Setup Firebase Project**:
   - Create a new Firebase project
   - Enable Authentication (Email/Password)
   - Create Realtime Database
   - Get configuration details

2. **Configure Files**:
   - Replace Firebase config in both HTML files
   - Set up database rules for security

3. **Deploy**:
   - Host files on any web server
   - Ensure HTTPS for production use

4. **Admin Setup**:
   - Create admin account via signup
   - Set QR code URL in admin settings
   - Start creating tournaments

## 📋 Usage Instructions

### For Users
1. Open `user.html` in mobile browser
2. Sign up with username, email, and password
3. Add money via QR code and transaction ID
4. Browse and join tournaments
5. Check "My Tournaments" for joined events
6. Withdraw money via UPI ID when needed

### For Admins
1. Open `admin.html` in mobile browser
2. Login with admin credentials
3. Set QR code URL in settings
4. Create tournaments with all details
5. Manage tournament status and room details
6. Approve wallet requests
7. Declare winners and credit prize money

## 🎨 Customization

### Colors
Modify the CSS gradient classes:
- `.gradient-primary`: Blue → Teal
- `.gradient-success`: Green → Teal  
- `.gradient-danger`: Red → Orange
- `.gradient-card`: Purple gradient

### Games
Add more games in the admin tournament creation dropdown:
```html
<option value="New Game">New Game</option>
```

### Styling
All styling uses TailwindCSS classes for easy customization and responsive design.

## 🔧 Development

The app is built with vanilla JavaScript and Firebase, making it easy to:
- Add new features
- Customize the UI
- Integrate with other services
- Deploy anywhere

## 📄 License

This project is open source and available under the MIT License.

---

**Built with ❤️ for the E-Sports community**
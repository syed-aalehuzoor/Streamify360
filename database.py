import pickle
import datetime
import schedule

class SubscriptionPlan():
    def __init__(self, name: str, price: int, features: list):
        self.name = name
        self.price = price
        self.features = features

plans = [
    SubscriptionPlan(
        name='Basic',
        price= 5,
        features= [
            'Feature 1',
            'Feature 2',
            'Feature 3',
            'Feature 4',
            'Feature 5'
            ]
        ),
    SubscriptionPlan(
        name='Standard',
        price= 15,
        features= [
            'Feature 1',
            'Feature 2',
            'Feature 3',
            'Feature 4',
            'Feature 5'
            ]
        ),
    SubscriptionPlan(
        name='Premium',
        price= 30,
        features= [
            'Feature 1',
            'Feature 2',
            'Feature 3',
            'Feature 4',
            'Feature 5'
            ]
        ),
    ]

class User:
    def __init__(self, userid: str, username: str, plan: SubscriptionPlan, startdate: None):
        self.userid = userid
        self.username = username
        self.plan = plan
        self.start_date = startdate

class Users:
    def __init__(self):     
        try:
            with open('database.pkl', 'rb') as file:
                self.users = pickle.load(file)
        except (FileNotFoundError):
            self.users = {}

    def save_json(self):
        with open('database.pkl', 'wb') as file:
            pickle.dump(self.users, file)
    
    def add_user(self, userid: str, username: str):
        if userid not in self.users:
            self.users[userid] = User(userid=userid, username=username, plan=plans[0], startdate=datetime.datetime.now())
        self.save_json()

    def subscribe(self, userid: str, plan: SubscriptionPlan):
        if userid in self.users:
            self.users[userid].plan = plan
            self.users[userid].start_date = datetime.now()  # Update start date 
        self.save_json()

    def get_user(self, userid: str):
        if userid in self.users:
            return self.users[userid]
        return False

    def unsubscribe_expired_users(self):
        for userid, user in list(self.users.items()):  # Use list() to avoid modifying dict during iteration
            if user.start_date:
                if datetime.now() - user.start_date > datetime.timedelta(days=30):
                    self.users[userid].plan = plans[0]
        self.save_json()

user_plans = Users()
schedule.every().day.at("00:01").do(job_func=user_plans.unsubscribe_expired_users)
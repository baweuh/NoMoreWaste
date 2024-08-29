# models.py
from sqlalchemy import create_engine, Column, Integer, Date, String, Time, Text, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship, sessionmaker
import config

Base = declarative_base()

class Tournee(Base):
    __tablename__ = 'Tournees'
    
    delivery_id = Column(Integer, primary_key=True, autoincrement=True)
    delivery_date = Column(Date, nullable=False)
    delivery_address = Column(String(255))  # Adresse de livraison
    status = Column(String(50))
    start_time = Column(Time)
    end_time = Column(Time)
    pdf_report_path = Column(Text)
    notes = Column(Text)  # Ajouté pour les notes
    
    # Foreign keys
    customer_id = Column(Integer, ForeignKey('Clients.customer_id'))
    
    # Relationships
    client = relationship("Client", back_populates="tournees")
    benevoles = relationship("BenevoleService", back_populates="tournee")

class Benevole(Base):
    __tablename__ = 'Benevoles'
    
    volunteer_id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(100), nullable=False)
    email = Column(String(100))
    phone = Column(String(15))
    skills = Column(Text)
    status = Column(String(50))
    
    # Relationships
    benevole_services = relationship("BenevoleService", back_populates="benevole")

class Service(Base):
    __tablename__ = 'Services'
    
    service_id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(100), nullable=False)
    description = Column(Text)
    
    # Relationships
    benevole_services = relationship("BenevoleService", back_populates="service")

class BenevoleService(Base):
    __tablename__ = 'Benevoles_Services'
    
    assignment_id = Column(Integer, primary_key=True, autoincrement=True)
    volunteer_id = Column(Integer, ForeignKey('Benevoles.volunteer_id'), nullable=False)
    service_id = Column(Integer, ForeignKey('Services.service_id'), nullable=False)
    
    # Relationships
    benevole = relationship("Benevole", back_populates="benevole_services")
    service = relationship("Service", back_populates="benevole_services")
    tournee = relationship("Tournee", back_populates="benevoles")

class Client(Base):
    __tablename__ = 'Clients'
    
    customer_id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(100), nullable=False)
    email = Column(String(100))
    phone = Column(String(15))
    
    # Relationships
    tournees = relationship("Tournee", back_populates="client")

# Create the database engine
engine = create_engine(config.DATABASE_URI)
Session = sessionmaker(bind=engine)
session = Session()
